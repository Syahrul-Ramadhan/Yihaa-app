<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LikeController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate([
            'post_id' => 'required|integer'
        ]);

        $userId = session('user_id'); // gunakan session usermu
        $postId = $request->post_id;

        // ======================================
        // 1. CEK apakah user sudah like
        // ======================================
        $checkQuery = <<<'GRAPHQL'
        query($userId: Int!, $postId: Int!) {
            likesCollection(filter: {
                user_id: { eq: $userId },
                post_id: { eq: $postId }
            }) {
                edges {
                    node {
                        like_id
                    }
                }
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $checkQuery,
            'variables' => [
                'userId' => $userId,
                'postId' => $postId,
            ]
        ]);

        $existingLike = $response->json('data.likesCollection.edges');

        // ======================================
        // 2. Jika SUDAH LIKE → hapus
        // ======================================
        if (!empty($existingLike)) {

            $likeId = $existingLike[0]['node']['like_id'];

            $deleteQuery = <<<'GRAPHQL'
            mutation($likeId: Int!) {
                deleteFromlikesCollection(filter: { like_id: { eq: $likeId } }) {
                    affectedCount
                }
            }
            GRAPHQL;

            Http::withHeaders([
                'apikey' => env('SUPABASE_SERVICE_KEY'),
                'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
            ])->post(env('SUPABASE_URL') . '/graphql/v1', [
                'query' => $deleteQuery,
                'variables' => [
                    'likeId' => $likeId
                ]
            ]);

            return response()->json([
                'status' => 'unliked'
            ]);
        }

        // ======================================
        // 3. Jika BELUM LIKE → insert
        // ======================================
        $insertQuery = <<<'GRAPHQL'
        mutation($postId: Int!, $userId: Int!) {
            insertIntolikesCollection(objects: {
                post_id: $postId,
                user_id: $userId
            }) {
                affectedCount
            }
        }
        GRAPHQL;

        Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $insertQuery,
            'variables' => [
                'postId' => $postId,
                'userId' => $userId
            ]
        ]);

        return response()->json([
            'status' => 'liked'
        ]);
    }
}