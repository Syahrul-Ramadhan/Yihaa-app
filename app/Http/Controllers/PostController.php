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
        $search = $request->input('search');
        $filter = null;

        if ($search) {
            $filter = [
                'caption' => ['ilike' => '%' . $search . '%']
            ];
        }

        // 1. Get Recommendations from AI
        $userId = session('user_id');
        $recommendedPostIds = [];

        if ($userId) {
            try {
                $recommendationService = new \App\Services\RecommendationService();
                $recommendedPostIds = $recommendationService->getRecommendations($userId, 20);
            } catch (\Exception $e) {
                // Fallback silently if AI fails
                \Illuminate\Support\Facades\Log::error("Recommendation failed: " . $e->getMessage());
            }
        }

        // 2. Build GraphQL Query
        // If we have recommendations, we filter by those IDs
        // Otherwise, we show latest posts (default behavior)

        $filterQuery = '';
        if (!empty($recommendedPostIds)) {
            // GraphQL filter: { post_id: { in: [1, 2, 3] } }
            $idsString = implode(',', $recommendedPostIds);
            $filterQuery = ", filter: { post_id: { in: [$idsString] } }";

            // Note: GraphQL doesn't support custom ordering by specific ID list easily without client-side sorting.
            // For now, we fetch them and will sort them in PHP to match recommendation order.
        } elseif ($search) {
            $filterQuery = ', filter: { caption: { ilike: "%' . $search . '%" } }';
        }

        $query = <<<GRAPHQL
        query {
            postsCollection(
                orderBy: [{ created_at: DescNullsLast }]
                $filterQuery
            ) {
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
                    }
                }
            }

            likes: likesCollection {
                edges {
                    node {
                        like_id
                        post_id
                        user_id
                    }
                }
            }

            comments: commentsCollection {
                edges {
                    node {
                        comment_id
                        post_id
                        comment_text
                        parent_comment_id
                        user_id
                        users {
                            name
                            avatar_url
                        }
                    }
                }
            }
        }
        GRAPHQL;

        $url = rtrim(env('SUPABASE_URL'), '/');

        $response = Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
            'Content-Type' => 'application/json',
        ])->post($url . '/graphql/v1', [
            'query' => $query
        ]);

        if ($response->failed()) {
            dd('Error HTTP: ' . $response->body());
        }

        if ($response->json('errors')) {
            dd('GraphQL Error: ', $response->json('errors'));
        }

        $data = $response->json('data');
        $userId = session('user_id');
        if (!$userId) {
            return back()->with('error', 'Please login first');
        }

        // ================================
        // GROUP LIKES PER POST
        // ================================
        $likesIndexed = collect($data['likes']['edges'])
            ->groupBy('node.post_id')
            ->map(fn($g) => $g->map(fn($l) => $l['node']));

        $likesGrouped = $likesIndexed->map(fn($g) => count($g));
        // ================================
        // GROUP COMMENTS PER POST + DETAIL
        // ================================
        $commentsGrouped = collect($data['comments']['edges'])
            ->groupBy('node.post_id')
            ->map(fn($g) => $g->map(fn($c) => $c['node']));

        // ================================
        // MAP POSTS
        // ================================
        $posts = collect($data['postsCollection']['edges'])->map(function ($edge) use ($likesGrouped, $commentsGrouped, $likesIndexed, $userId) {
            $post = $edge['node'];
            $id = $post['post_id'];

            $isLiked = false;

            if (isset($likesIndexed[$id])) {
                $isLiked = $likesIndexed[$id]->contains(function ($like) use ($userId) {
                    return $like['user_id'] == $userId;
                });
            }

            return [
                'post_id' => $id,
                'caption' => $post['caption'],
                'image_url' => $post['image_url'],
                'created_at' => $post['created_at'],
                'uploaded_by' => $post['uploaded_by'],
                'uploader_name' => $post['users']['name'] ?? 'Unknown',
                'uploader_avatar' => $post['users']['avatar_url'] ?? null,

                'likes_count' => $likesGrouped[$id] ?? 0,
                'is_liked'    => $isLiked,

                'comments_count' => isset($commentsGrouped[$id]) ? count($commentsGrouped[$id]) : 0,

                // daftar komentar dipakai untuk pop-up
                'comments' => $commentsGrouped[$id] ?? [],
            ];
        });

        // Sort posts if we have recommendations
        if (!empty($recommendedPostIds)) {
            // Create a lookup map for sort order: [post_id => index]
            $sortOrder = array_flip($recommendedPostIds);

            $posts = $posts->sortBy(function ($post) use ($sortOrder) {
                return $sortOrder[$post['post_id']] ?? 999999;
            })->values();
        }

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

    public function show($id)
    {
        // Ambil post utama
        $post = Post::with([
            'user',
            'comments' => function ($q) {
                $q->whereNull('parent_id')->with('user', 'replies.user');
            }
        ])->findOrFail($id);

        return view('posts.show', compact('post'));
    }
}
