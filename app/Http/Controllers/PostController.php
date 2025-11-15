<?php
namespace App\Http\Controllers;
use App\Helpers\SupabaseHelper;
use App\Models\Post;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        // Query GraphQL untuk ambil data teams
        $query = <<<'GRAPHQL'
        query {
            postsCollection {
                edges {
                    node {
                        post_id
                        caption
                        image_url
                        created_at
                        uploaded_by
                        users {
                            name
                            avatar_url
                        }
                        likesCollection {
                            edges {
                                node {
                                    like_id
                                    user_id
                                }
                            }
                        }
                        commentsCollection {
                            edges {
                                node {
                                    comment_id
                                    user_id
                                    content
                                }
                            }
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
        ]);

        if ($response->failed()) {
            dd('Error GraphQL: ' . $response->body());
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
                'user' => $node['users'],
                'likes_count' => count($node['likesCollection']['edges'] ?? []),
                'comments_count' => count($node['commentsCollection']['edges'] ?? []),
            ];
        });

        return view('pages.users.home', compact('posts'));
    }

    // public function testInsert()
    // {
    //     $post = new \App\Models\Post();

    //     $post->caption = "Ini postingan percobaan untuk uji insert ke Supabase.";
    //     $post->image_url = "https://via.placeholder.com/600x300.png?text=Contoh+Gambar";
    //     $post->uploaded_by = 1;
    //     $post->created_at = now();

    //     $post->save();

    //     return "✅ Data berhasil ditambahkan! ID baru: " . $post->post_id;
    // }

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