<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CommentController extends Controller
{
    public function fetch($postId)
    {
        $query = <<<'GRAPHQL'
        query($postId: Int!) {
            commentsCollection(filter: { post_id: { eq: $postId } }) {
                edges {
                    node {
                        comment_id
                        post_id
                        comment_text
                        parent_comment_id
                        created_at
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

        $res = Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_KEY'),
            'Authorization' => 'Bearer '.env('SUPABASE_SERVICE_KEY'),
        ])->post(env('SUPABASE_URL').'/graphql/v1', [
            'query' => $query,
            'variables' => [ 'postId' => $postId ]
        ]);

        // dd($res->json());

        $list = collect($res->json('data.commentsCollection.edges'))->pluck('node');

        // pisahkan parent dan reply
        $parents = $list->filter(fn($c) => !$c['parent_comment_id'])->values();

        foreach ($parents as &$p) {
            $p['user_name']  = $p['users']['name'] ?? 'User';
            $p['avatar_url'] = $p['users']['avatar_url'] ?? null;

            $p['time_human'] = \Carbon\Carbon::parse($p['created_at'])->diffForHumans();
            $p['showReplies'] = false;

            // balasan
            $p['replies'] = $list->filter(fn($r) => $r['parent_comment_id'] == $p['comment_id'])
                ->map(fn($r) => [
                    'comment_id' => $r['comment_id'],
                    'comment_text' => $r['comment_text'],
                    'user_name' => $r['users']['name'] ?? 'User',
                    'avatar_url' => $r['users']['avatar_url'] ?? null,
                    'time_human' => \Carbon\Carbon::parse($r['created_at'])->diffForHumans(),
                ])
                ->values();
        }


        return response()->json([
            'comments' => $parents
        ]);
    }

public function add(Request $request)
{
    $userId = session('user_id');

    $query = <<<'GRAPHQL'
    mutation($postId: Int!, $userId: Int!, $text: String!) {
        insertIntocommentsCollection(objects: {
            post_id: $postId,
            user_id: $userId,
            comment_text: $text
        }) {
            affectedCount
        }
    }
    GRAPHQL;

    Http::withHeaders([
        'apikey' => env('SUPABASE_SERVICE_KEY'),
        'Authorization' => 'Bearer '.env('SUPABASE_SERVICE_KEY'),
    ])->post(env('SUPABASE_URL').'/graphql/v1', [
        'query' => $query,
        'variables' => [
            'text' => $request->comment_text,
            'postId' => $request->post_id,
            'userId' => $userId
        ]
    ]);

    return response()->json(['success' => true]);
}

}