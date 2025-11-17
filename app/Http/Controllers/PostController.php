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
                'uploader_name' => $node['users']['name'] ?? 'Unknown',
                'uploader_avatar' => $node['users']['avatar_url'] ?? null,
                'likes_count' => 0, // Default karena tabel likes belum ada
                'comments_count' => 0, // Default karena tabel comments belum ada
            ];
        });

        return view('pages.users.home', compact('posts'));
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
        $post = new \App\Models\Post();
        
        $post->caption = $request->input('caption'); // Ambil dari form
        $post->image_url = $imageUrl; // Hasil upload (bisa null)
        
        // Ganti '1' dengan ID user yang sedang login
        // Jika sistem login Anda sudah jalan, gunakan Auth::id()
        $post->uploaded_by = 1;
        // $post->uploaded_by = Auth::id(); // GUNAKAN INI JIKA SUDAH LOGIN
        
        $post->created_at = now();

        $post->save(); // Model akan mengirim ini ke Supabase

        // 5. Kembalikan ke halaman home
        return redirect()->route('posts.index')->with('success', 'Postingan berhasil ditambahkan!');
    }
}