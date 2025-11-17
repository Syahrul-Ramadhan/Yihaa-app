<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Team;

class TeamController extends Controller
{
    protected $supabaseUrl;
    protected $supabaseKey;

    public function __construct()
    {
        $this->supabaseUrl = env('SUPABASE_URL') . '/graphql/v1';
        $this->supabaseKey = env('SUPABASE_ANON_KEY');
    }

    public function index()
    {
        // Query GraphQL untuk ambil data teams
        $query = <<<'GRAPHQL'
        query {
            teamsCollection {
                edges {
                    node {
                        team_id
                        team_name
                        team_desc
                        team_status
                        member_count
                        member_limit
                        leader_id
                    }
                }
            }
        }
        GRAPHQL;

        // Request ke Supabase GraphQL endpoint
        $response = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $query
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to fetch data'], 500);
        }

        // Ambil data dari edges → node
        $edges = $response->json('data.teamsCollection.edges') ?? [];
        $teams = array_map(fn($edge) => $edge['node'], $edges);

        return view('pages.users.team', compact('teams'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'team_name' => 'required|string|max:100',
            'team_desc' => 'nullable|string',
            'member_limit' => 'required|integer|min:2|max:50',
            'team_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userId = session('user_id');
        if (!$userId) {
            return back()->with('error', 'Please login first');
        }

        $teamLogoUrl = null;

        // Upload logo to Supabase Storage if provided
        if ($request->hasFile('team_logo')) {
            $file = $request->file('team_logo');
            $fileName = 'team_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $fileContent = file_get_contents($file->getRealPath());

            // Upload to Supabase Storage
            $uploadResponse = Http::withHeaders([
                'apikey' => env('SUPABASE_SERVICE_KEY'),
                'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
                'Content-Type' => $file->getMimeType(),
            ])->send('POST', env('SUPABASE_URL') . '/storage/v1/object/team-logos/' . $fileName, [
                'body' => $fileContent
            ]);

            if ($uploadResponse->successful()) {
                $teamLogoUrl = env('SUPABASE_URL') . '/storage/v1/object/public/team-logos/' . $fileName;
            }
        }

        // Insert team via GraphQL
        $mutation = <<<'GRAPHQL'
        mutation InsertTeam($team_name: String!, $team_desc: String, $team_logo: String, $leader_id: BigInt!, $member_limit: Int!) {
            insertIntoteamsCollection(
                objects: {
                    team_name: $team_name,
                    team_desc: $team_desc,
                    team_logo: $team_logo,
                    leader_id: $leader_id,
                    member_count: 1,
                    member_limit: $member_limit,
                    team_status: "open"
                }
            ) {
                affectedCount
                records {
                    team_id
                    team_name
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
                'team_name' => $request->team_name,
                'team_desc' => $request->team_desc,
                'team_logo' => $teamLogoUrl,
                'leader_id' => $userId,
                'member_limit' => $request->member_limit,
            ],
        ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            return back()->with('error', 'Failed to create team');
        }

        return redirect()->route('teams.index')->with('success', 'Team created successfully!');
    }

    public function show($team_id)
    {
        $userId = session('user_id');

        // Query team detail
        $teamQuery = <<<GRAPHQL
        query {
            teamsCollection(filter: { team_id: { eq: $team_id } }) {
                edges {
                    node {
                        team_id
                        team_name
                        team_desc
                        team_logo
                        team_status
                        member_count
                        member_limit
                        leader_id
                    }
                }
            }
        }
        GRAPHQL;

        $teamResponse = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $teamQuery
        ]);

        $teamEdges = $teamResponse->json('data.teamsCollection.edges') ?? [];
        if (empty($teamEdges)) {
            return redirect()->route('teams.index')->with('error', 'Team not found');
        }
        $team = $teamEdges[0]['node'];

        // Query team members
        $membersQuery = <<<GRAPHQL
        query {
            team_membersCollection(filter: { team_id: { eq: $team_id }, status: { eq: "accepted" } }) {
                edges {
                    node {
                        user_id
                        role
                        users {
                            name
                            avatar_url
                        }
                    }
                }
            }
        }
        GRAPHQL;

        $membersResponse = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $membersQuery
        ]);

        $membersEdges = $membersResponse->json('data.team_membersCollection.edges') ?? [];
        $members = array_map(function($edge) {
            return [
                'user_id' => $edge['node']['user_id'],
                'role' => $edge['node']['role'],
                'name' => $edge['node']['users']['name'],
                'avatar_url' => $edge['node']['users']['avatar_url'] ?? null,
            ];
        }, $membersEdges);

        // Check if user is member
        $isMember = collect($members)->contains('user_id', $userId);

        // Check if user has pending request
        $pendingQuery = <<<GRAPHQL
        query {
            team_membersCollection(filter: { team_id: { eq: $team_id }, user_id: { eq: $userId }, status: { eq: "pending" } }) {
                edges {
                    node {
                        member_id
                    }
                }
            }
        }
        GRAPHQL;

        $pendingResponse = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $pendingQuery
        ]);

        $isPending = !empty($pendingResponse->json('data.team_membersCollection.edges'));

        return view('pages.users.team-detail', compact('team', 'members', 'isMember', 'isPending'));
    }

    public function join(Request $request, $team_id)
    {
        $userId = session('user_id');
        if (!$userId) {
            return back()->with('error', 'Please login first');
        }

        // Get team leader
        $teamQuery = <<<GRAPHQL
        query {
            teamsCollection(filter: { team_id: { eq: $team_id } }) {
                edges {
                    node {
                        leader_id
                        team_name
                    }
                }
            }
        }
        GRAPHQL;

        $teamResponse = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $teamQuery
        ]);

        $teamEdges = $teamResponse->json('data.teamsCollection.edges') ?? [];
        if (empty($teamEdges)) {
            return back()->with('error', 'Team not found');
        }
        $team = $teamEdges[0]['node'];

        // Insert join request
        $mutation = <<<'GRAPHQL'
        mutation InsertMember($team_id: BigInt!, $user_id: BigInt!) {
            insertIntoteam_membersCollection(
                objects: {
                    team_id: $team_id,
                    user_id: $user_id,
                    role: "member",
                    status: "pending"
                }
            ) {
                affectedCount
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
                'team_id' => $team_id,
                'user_id' => $userId,
            ],
        ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            return back()->with('error', 'Failed to send join request');
        }

        // Create notification for team leader
        $userName = session('user_name');
        $notifMutation = <<<'GRAPHQL'
        mutation InsertNotification($user_id: BigInt!, $from_user_id: BigInt!, $team_id: BigInt!, $message: String!) {
            insertIntonotificationsCollection(
                objects: {
                    user_id: $user_id,
                    from_user_id: $from_user_id,
                    team_id: $team_id,
                    type: "team_join_request",
                    message: $message,
                    is_read: false
                }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
            'Content-Type' => 'application/json',
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $notifMutation,
            'variables' => [
                'user_id' => $team['leader_id'],
                'from_user_id' => $userId,
                'team_id' => $team_id,
                'message' => "$userName wants to join your team: {$team['team_name']}",
            ],
        ]);

        return back()->with('success', 'Join request sent! Waiting for approval.');
    }

    // public function viewTeam()
    // {
    //     return view('pages.users.team');
    // }
 
}