<?php
namespace App\Http\Controllers;
use App\Helpers\SupabaseHelper;
use App\Models\Post;

use Illuminate\Support\Facades\Http;

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
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
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

    public function testInsert()
    {
        $post = new \App\Models\Post();

        $post->caption = "Ini postingan percobaan untuk uji insert ke Supabase.";
        $post->image_url = "https://via.placeholder.com/600x300.png?text=Contoh+Gambar";
        $post->uploaded_by = 1;
        $post->created_at = now();

        $post->save();

        return "✅ Data berhasil ditambahkan! ID baru: " . $post->post_id;
    }


}
