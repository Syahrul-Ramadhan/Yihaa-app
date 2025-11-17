<?php
namespace App\Http\Controllers;
use App\Helpers\SupabaseHelper;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TeamChatController extends Controller
{
    public function index()
    {
        $userId = session('user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        // Query teams yang user ikuti (accepted members only)
        $query = <<<GRAPHQL
        query {
            team_membersCollection(filter: { user_id: { eq: $userId }, status: { eq: "accepted" } }) {
                edges {
                    node {
                        teams {
                            team_id
                            team_name
                            team_desc
                            team_logo
                            member_count
                            member_limit
                        }
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

        $edges = $response->json('data.team_membersCollection.edges') ?? [];
        $teams = array_map(fn($edge) => $edge['node']['teams'], $edges);

        return view('pages.users.messages', [
            'teams' => $teams,
            'activeTeam' => null,
            'messages' => [],
        ]);
    }

    public function show($team_id)
    {
        $userId = session('user_id');
        $userRole = session('role');

        // Admin bisa akses semua chat
        if ($userRole !== 'admin') {
            // Check apakah user adalah member yang sudah accepted
            $memberCheckQuery = <<<GRAPHQL
            query {
                team_membersCollection(filter: { team_id: { eq: $team_id }, user_id: { eq: $userId }, status: { eq: "accepted" } }) {
                    edges {
                        node {
                            member_id
                        }
                    }
                }
            }
            GRAPHQL;

            $memberCheckResponse = Http::withHeaders([
                'apikey' => env('SUPABASE_ANON_KEY'),
                'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
                'Content-Type' => 'application/json'
            ])->post(env('SUPABASE_URL') . '/graphql/v1', [
                'query' => $memberCheckQuery
            ]);

            $memberEdges = $memberCheckResponse->json('data.team_membersCollection.edges') ?? [];
            
            if (empty($memberEdges)) {
                return redirect()->route('chat.index')->with('error', 'You must be an accepted member to access this chat');
            }
        }

        // Query ambil list tim yang user ikuti (accepted members only)
        $teamsQuery = <<<GRAPHQL
        query {
            team_membersCollection(filter: { user_id: { eq: $userId }, status: { eq: "accepted" } }) {
                edges {
                    node {
                        teams {
                            team_id
                            team_name
                            team_desc
                            team_logo
                        }
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

        $teamsEdges = $teamsResponse->json('data.team_membersCollection.edges') ?? [];
        $teams = array_map(fn($edge) => $edge['node']['teams'], $teamsEdges);

        // Query ambil pesan dari tim tertentu - PENTING: hapus quotes di filter
        $messagesQuery = <<<GRAPHQL
        query {
            chat_teamCollection(filter: { team_id: { eq: $team_id } }, orderBy: { created_at: AscNullsFirst }) {
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

        // Debug jika perlu
        if ($messagesResponse->failed()) {
            \Log::error('Chat messages query failed', [
                'response' => $messagesResponse->body(),
                'team_id' => $team_id
            ]);
        }

        $msgEdges = $messagesResponse->json('data.chat_teamCollection.edges') ?? [];
        $messages = array_map(fn($edge) => $edge['node'], $msgEdges);

        // Ambil tim aktif
        $activeTeam = collect($teams)->firstWhere('team_id', (int)$team_id);

        // Debug temporary - HAPUS INI SETELAH BERHASIL
        if (!$activeTeam) {
            dd([
                'team_id' => $team_id,
                'teams' => $teams,
                'activeTeam' => $activeTeam,
                'messages_response' => $messagesResponse->json(),
            ]);
        }

        // Pastikan activeTeam ada
        if (!$activeTeam) {
            return redirect()->route('chat.index')->with('error', 'Team not found');
        }

        return view('pages.users.messages', compact('teams', 'activeTeam', 'messages'));
    }

    public function sendMessage(Request $request, $team_id)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userId = session('user_id');
        $userRole = session('role');

        if (!$userId) {
            return back()->with('error', 'Please login first');
        }

        // Admin bisa kirim ke semua team
        if ($userRole !== 'admin') {
            // Check apakah user adalah member yang sudah accepted
            $memberCheckQuery = <<<GRAPHQL
            query {
                team_membersCollection(filter: { team_id: { eq: $team_id }, user_id: { eq: $userId }, status: { eq: "accepted" } }) {
                    edges {
                        node {
                            member_id
                        }
                    }
                }
            }
            GRAPHQL;

            $memberCheckResponse = Http::withHeaders([
                'apikey' => env('SUPABASE_ANON_KEY'),
                'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
                'Content-Type' => 'application/json'
            ])->post(env('SUPABASE_URL') . '/graphql/v1', [
                'query' => $memberCheckQuery
            ]);

            $memberEdges = $memberCheckResponse->json('data.team_membersCollection.edges') ?? [];
            
            if (empty($memberEdges)) {
                return back()->with('error', 'You must be an accepted member to send messages');
            }
        }

        // Insert message via GraphQL
        $mutation = <<<'GRAPHQL'
        mutation InsertMessage($team_id: BigInt!, $user_id: BigInt!, $text_chat: String!) {
            insertIntochat_teamCollection(
                objects: {
                    team_id: $team_id,
                    user_id: $user_id,
                    text_chat: $text_chat
                }
            ) {
                affectedCount
                records {
                    chat_id
                    text_chat
                }
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
            'Content-Type' => 'application/json',
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $mutation,
            'variables' => [
                'team_id' => (int)$team_id,
                'user_id' => (int)$userId,
                'text_chat' => $request->message,
            ],
        ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            return back()->with('error', 'Failed to send message');
        }

        return redirect()->route('chat.show', $team_id)->with('success', 'Message sent!');
    }
}
