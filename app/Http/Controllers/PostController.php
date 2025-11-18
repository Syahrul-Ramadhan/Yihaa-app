<?php
namespace App\Http\Controllers;
use App\Helpers\SupabaseHelper;
use App\Models\Post;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index(Request $request)
    {
        // 1. Tangkap input pencarian
        $search = $request->input('search');

        $filter = null; // Default: tidak ada filter
        if ($search) {
            $filter = [
                'caption' => [
                    'ilike' => '%' . $search . '%'
                ]
            ];
        }

        // 2. Query GraphQL - PERBAIKAN FINAL (KEMBALI KE postsCollection)
        $query = <<<'GRAPHQL'
        query($filter: postsFilter) {
            
            # ▼▼▼ UBAH KEMBALI KE 'postsCollection' ▼▼▼
            postsCollection(
                filter: $filter, 
                
                # ▼ SINTAKS 'orderBy' YANG BENAR ▼
                orderBy: [{ created_at: DescNullsLast }]
            
            ) {
                edges {
                    node {
                        post_id
                        caption
                        image_url
                        created_at
                        uploaded_by
                        
                        # --- RELASI KE USERS ---
                        users {
                            name
                            avatar_url
                        }
                    }
                }
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
            'Content-Type' => 'application/json',
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $query,
            'variables' => [
                'filter' => $filter
            ]
        ]);

        if ($response->failed()) {
            dd('Error HTTP Gagal: ' . (string) $response->body());
        }
        
        // ▼▼▼ BIARKAN INI TETAP ADA SAMPAI BERHASIL ▼▼▼
        if ($response->json('errors')) {
             dd('Error payload GraphQL: ', $response->json('errors'));
        }
        
        // Ambil node data
        $edges = $response->json('data.postsCollection.edges') ?? [];

        // Ubah ke bentuk array Laravel-friendly
        $posts = collect($edges)->map(function ($edge) {
            $node = $edge['node'];
            return [
                'post_id' => $node['post_id'],
                'caption' => $node['caption'],
                'image_url' => $node['image_url'],
                'created_at' => $node['created_at'],
                'uploaded_by' => $node['uploaded_by'], // Tambahkan ini untuk validasi delete
                'uploader_name' => $node['users']['name'] ?? 'Unknown',
                'uploader_avatar' => $node['users']['avatar_url'] ?? null,
                'likes_count' => 0, // Default karena tabel likes belum ada
                'comments_count' => 0, // Default karena tabel comments belum ada
            ];
        });

        // Get team recommendations
        $teams = \App\Http\Controllers\TeamController::getTeamRecommendations(20);

        return view('pages.users.home', compact('posts', 'teams'));
    }

    public function store(Request $request)
    {
        // 1. Validasi input dari form
        $request->validate([
            'caption' => 'required|string|max:2000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imageUrl = null;

        // 2. Proses upload gambar (jika ada)
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            
            $bucketName = 'post-images'; // Ini sudah benar
            
            // ▼ PERUBAHAN 1: Hapus $bucketName . '/' dari sini
            $fileName = time() . '_' . $file->getClientOriginalName();
            
           // Upload ke Supabase Storage
            $storageResponse = Http::withHeaders([
                'apikey' => env('SUPABASE_SERVICE_KEY'),
                'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
            ])
            ->withBody(
                $file->getContent(), 
                $file->getMimeType()
            )
            // ▼ PERUBAHAN 2: Ubah URL post. Hapus '/public/' dan masukkan $bucketName
            ->post(env('SUPABASE_URL') . '/storage/v1/object/' . $bucketName . '/' . $fileName);

            if ($storageResponse->failed()) {
                // Tampilkan pesan error yang lebih jelas untuk debugging
                // dd($storageResponse->json()); 
                return back()->with('error', 'Gagal mengupload gambar. (Storage API Error)');
            }
            
            // ▼ PERUBAHAN 3: Tambahkan $bucketName di URL publik
            $imageUrl = env('SUPABASE_URL') . '/storage/v1/object/public/' . $bucketName . '/' . $fileName;
        }

        // 4. Simpan data ke database (menggunakan Model)
        $post = new Post();
        
        $post->caption = $request->input('caption'); // Ambil dari form
        $post->image_url = $imageUrl ?? ''; // Set empty string jika null (karena NOT NULL constraint)
        
        // Ambil user_id dari session
        $userId = session('user_id');
        if (!$userId) {
            return back()->with('error', 'Please login first');
        }
        
        $post->uploaded_by = $userId; // User yang sedang login
        $post->created_at = now();

        $post->save(); // Model akan mengirim ini ke Supabase

        
        // 5. Kembalikan ke halaman home
        return redirect()->route('posts.index')->with('success', 'Postingan berhasil ditambahkan!');
    }

    public function destroy($post_id)
    {
        $userId = session('user_id');
        $userRole = session('role');

        if (!$userId) {
            return back()->with('error', 'Please login first');
        }

        // Query post untuk validasi ownership
        $postQuery = <<<GRAPHQL
        query {
            postsCollection(filter: { post_id: { eq: $post_id } }) {
                edges {
                    node {
                        post_id
                        uploaded_by
                        image_url
                    }
                }
            }
        }
        GRAPHQL;

        $postResponse = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $postQuery
        ]);

        $postEdges = $postResponse->json('data.postsCollection.edges') ?? [];
        
        if (empty($postEdges)) {
            return back()->with('error', 'Post not found');
        }

        $post = $postEdges[0]['node'];

        // Validasi: hanya owner atau admin yang bisa delete
        if ($userRole !== 'admin' && $post['uploaded_by'] != $userId) {
            return back()->with('error', 'You are not authorized to delete this post');
        }

        // Delete post via GraphQL
        $mutation = <<<GRAPHQL
        mutation {
            deleteFrompostsCollection(filter: { post_id: { eq: $post_id } }) {
                affectedCount
            }
        }
        GRAPHQL;

        $deleteResponse = Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
            'Content-Type' => 'application/json',
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $mutation
        ]);

        if ($deleteResponse->failed() || isset($deleteResponse->json()['errors'])) {
            return back()->with('error', 'Failed to delete post');
        }

        // Optional: Hapus gambar dari storage jika ada
        if (!empty($post['image_url'])) {
            // Extract filename from URL
            $fileName = basename(parse_url($post['image_url'], PHP_URL_PATH));
            
            Http::withHeaders([
                'apikey' => env('SUPABASE_SERVICE_KEY'),
                'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
            ])->delete(env('SUPABASE_URL') . '/storage/v1/object/post-images/' . $fileName);
        }

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully!');
    }
}
