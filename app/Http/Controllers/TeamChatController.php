<?php
namespace App\Http\Controllers;
use App\Helpers\SupabaseHelper;
use App\Models\Post;

use Illuminate\Support\Facades\Http;

class TeamChatController extends Controller
{
    public function index()
    {
        $query = <<<'GRAPHQL'
        query {
            teamsCollection {
                edges {
                    node {
                        team_id
                        team_name
                        team_desc
                        team_icon
                        member_count
                        member_limit
                    }
                }
            }
        }
        GRAPHQL;

        // Request ke Supabase GraphQL
        $response = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $query
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to fetch teams'], 500);
        }

        $edges = $response->json('data.teamsCollection.edges') ?? [];
        $teams = array_map(fn($edge) => $edge['node'], $edges);

        return view('pages.users.messages', [
            'teams' => $teams,
            'activeTeam' => null,
            'messages' => [],
        ]);
    }

    public function show($team_id)
    {
        // Query ambil list tim
        $teamsQuery = <<<'GRAPHQL'
        query {
            teamsCollection {
                edges {
                    node {
                        team_id
                        team_name
                        team_desc
                        team_icon
                    }
                }
            }
        }
        GRAPHQL;

        $teamsResponse = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $teamsQuery
        ]);

        $teamsEdges = $teamsResponse->json('data.teamsCollection.edges') ?? [];
        $teams = array_map(fn($edge) => $edge['node'], $teamsEdges);

        // Query ambil pesan dari tim tertentu
        $messagesQuery = <<<GRAPHQL
        query {
            chat_teamCollection(filter: { team_id: { eq: "$team_id" } }) {
                edges {
                    node {
                        chat_id
                        team_id
                        user_id
                        text_chat
                        file_chat
                        created_at
                        users {
                            name
                            avatar_url
                        }
                    }
                }
            }
        }
        GRAPHQL;

        $messagesResponse = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $messagesQuery
        ]);

        $msgEdges = $messagesResponse->json('data.chat_teamCollection.edges') ?? [];
        $messages = array_map(fn($edge) => $edge['node'], $msgEdges);

        // Ambil tim aktif
        $activeTeam = collect($teams)->firstWhere('team_id', $team_id);

        return view('pages.users.messages', compact('teams', 'activeTeam', 'messages'));
    }
}
