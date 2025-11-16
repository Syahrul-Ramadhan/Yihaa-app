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

        $filter = null;
        if ($search) {
            $filter = [
                'caption' => ['ilike' => "%{$search}%"]
            ];
        }

        // 2. Query GraphQL
        $query = <<<'GRAPHQL'
        query($filter: postsFilter) {
            postsCollection(
                filter: $filter
                orderBy: [{ created_at: DescNullsLast }]
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
                        likesCollection {
                            edges {
                                node {
                                    like_id
                                }
                            }
                        }
                        commentsCollection {
                            edges {
                                node {
                                    comment_id
                                }
                            }
                        }
                    }
                }
            }
        }
        GRAPHQL;

        // 3. Execute GraphQL dengan ANON_KEY (untuk read)
        $response = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json',
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $query,
            'variables' => ['filter' => $filter]
        ]);

        // 4. Handle errors
        if ($response->failed()) {
            \Log::error('PostController GraphQL Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return view('pages.users.home', [
                'posts' => [],
                'isLoggedIn' => session('supabase_token') !== null,
                'error' => 'Failed to load posts'
            ]);
        }

        $data = $response->json();
        
        // Check GraphQL errors
        if (isset($data['errors'])) {
            \Log::error('PostController GraphQL Payload Error', [
                'errors' => $data['errors']
            ]);
            
            return view('pages.users.home', [
                'posts' => [],
                'isLoggedIn' => session('supabase_token') !== null,
                'error' => 'GraphQL error: ' . ($data['errors'][0]['message'] ?? 'Unknown error')
            ]);
        }

        // 5. Extract and transform posts
        $edges = $data['data']['postsCollection']['edges'] ?? [];
        
        $posts = collect($edges)->map(function ($edge) {
            $node = $edge['node'];
            return [
                'post_id' => $node['post_id'],
                'caption' => $node['caption'],
                'image_url' => $node['image_url'],
                'created_at' => $node['created_at'],
                'user' => $node['users'] ?? null,
                'likes_count' => count($node['likesCollection']['edges'] ?? []),
                'comments_count' => count($node['commentsCollection']['edges'] ?? []),
            ];
        });

        // 6. Check if user logged in
        $isLoggedIn = session('supabase_token') !== null;

        return view('pages.users.home', [
            'posts' => $posts,
            'isLoggedIn' => $isLoggedIn
        ]);
    }

    public function store(Request $request)
    {
        // 1. Check if user logged in
        $supabaseToken = session('supabase_token');
        if (!$supabaseToken) {
            return redirect()->route('login')->with('error', 'Please login to create post');
        }

        // 2. Validate input
        $request->validate([
            'caption' => 'required|string|max:2000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 3. Get current user ID
        $userResponse = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . $supabaseToken,
        ])->get(env('SUPABASE_URL') . '/auth/v1/user');

        if ($userResponse->failed()) {
            return back()->with('error', 'Failed to verify user');
        }

        $user = $userResponse->json();
        $userId = $user['id'];

        // 4. Upload image if exists
        $imageUrl = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $bucketName = 'post-images';
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Upload to Supabase Storage
            $storageResponse = Http::withHeaders([
                'apikey' => env('SUPABASE_SERVICE_ROLE_KEY'),
                'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_ROLE_KEY'),
            ])->withBody(
                $file->getContent(),
                $file->getMimeType()
            )->post(env('SUPABASE_URL') . '/storage/v1/object/' . $bucketName . '/' . $fileName);

            if ($storageResponse->failed()) {
                \Log::error('Image Upload Error', [
                    'status' => $storageResponse->status(),
                    'body' => $storageResponse->body()
                ]);
                
                return back()->with('error', 'Failed to upload image');
            }

            $imageUrl = env('SUPABASE_URL') . '/storage/v1/object/public/' . $bucketName . '/' . $fileName;
        }

        // 5. Insert post via GraphQL
        $mutation = <<<'GRAPHQL'
        mutation InsertPost($caption: String!, $image_url: String, $uploaded_by: UUID!) {
            insertIntopostsCollection(objects: [{
                caption: $caption
                image_url: $image_url
                uploaded_by: $uploaded_by
            }]) {
                records {
                    post_id
                    caption
                    image_url
                    created_at
                }
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_ROLE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_ROLE_KEY'),
            'Content-Type' => 'application/json',
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $mutation,
            'variables' => [
                'caption' => $request->caption,
                'image_url' => $imageUrl,
                'uploaded_by' => $userId
            ]
        ]);

        if ($response->failed()) {
            \Log::error('Post Creation Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return back()->with('error', 'Failed to create post');
        }

        $data = $response->json();
        
        if (isset($data['errors'])) {
            \Log::error('Post Creation GraphQL Error', [
                'errors' => $data['errors']
            ]);
            
            return back()->with('error', 'Failed to create post: ' . ($data['errors'][0]['message'] ?? 'Unknown error'));
        }

        return redirect()->route('home')->with('success', 'Post created successfully!');
    }

    public function testInsert()
    {
        // Test function untuk verify GraphQL connection
        $mutation = <<<'GRAPHQL'
        mutation {
            insertIntopostsCollection(objects: [{
                caption: "Test post from controller"
                uploaded_by: "00000000-0000-0000-0000-000000000000"
            }]) {
                records {
                    post_id
                    caption
                }
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_ROLE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_ROLE_KEY'),
            'Content-Type' => 'application/json',
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $mutation
        ]);

        return response()->json([
            'status' => $response->status(),
            'data' => $response->json()
        ]);
    }
}