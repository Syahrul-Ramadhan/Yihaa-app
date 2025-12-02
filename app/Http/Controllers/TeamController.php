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

    public function index(Request $request)
    {
        $userId = session('user_id');
        $search = $request->input('search');

        // Build filter hanya jika ada search query
        $filterClause = '';
        if (!empty($search)) {
            // Escape quotes dan build filter
            $searchEscaped = str_replace('"', '\\"', $search);
            $filterClause = '(filter: { team_name: { ilike: "%' . $searchEscaped . '%" } })';
        }

        // Query GraphQL untuk ambil semua teams (atau filtered jika ada search)
        $query = <<<GRAPHQL
        query {
            teamsCollection$filterClause {
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

        // Query untuk ambil teams yang user sudah join
        $myTeams = [];
        if ($userId) {
            $myTeamsQuery = <<<GRAPHQL
            query {
                team_membersCollection(filter: { user_id: { eq: $userId }, status: { eq: "accepted" } }) {
                    edges {
                        node {
                            team_id
                            role
                            teams {
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
            }
            GRAPHQL;

            $myTeamsResponse = Http::withHeaders([
                'apikey' => env('SUPABASE_ANON_KEY'),
                'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
                'Content-Type' => 'application/json'
            ])->post(env('SUPABASE_URL') . '/graphql/v1', [
                'query' => $myTeamsQuery
            ]);

            if ($myTeamsResponse->successful()) {
                $myTeamsEdges = $myTeamsResponse->json('data.team_membersCollection.edges') ?? [];
                $myTeams = array_map(function($edge) {
                    $teamData = $edge['node']['teams'];
                    $teamData['user_role'] = $edge['node']['role'];
                    return $teamData;
                }, $myTeamsEdges);
            }
        }

        return view('pages.users.team', compact('teams', 'myTeams', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'team_name' => 'required|string|max:100',
            'team_desc' => 'nullable|string',
            'member_limit' => 'required|integer|min:2|max:50',
            'team_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $userId = session('user_id');
        if (!$userId) {
            return back()->with('error', 'Please login first');
        }

        $teamLogoUrl = null;

        // Upload logo to Supabase Storage if provided
        if ($request->hasFile('team_logo')) {
            $file = $request->file('team_logo');
            $bucketName = 'team-icons'; // Use existing bucket
            $fileName = 'team_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            try {
                // Upload to Supabase Storage using attach for streaming (better performance)
                $uploadResponse = Http::timeout(30) // 30 second timeout
                    ->withHeaders([
                        'apikey' => env('SUPABASE_SERVICE_KEY'),
                        'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
                    ])
                    ->attach(
                        'file',
                        fopen($file->getRealPath(), 'r'),
                        $fileName
                    )
                    ->post(env('SUPABASE_URL') . '/storage/v1/object/' . $bucketName . '/' . $fileName);

                if ($uploadResponse->successful()) {
                    $teamLogoUrl = env('SUPABASE_URL') . '/storage/v1/object/public/' . $bucketName . '/' . $fileName;
                } else {
                    \Log::warning('Failed to upload team logo, creating team without logo', [
                        'response' => $uploadResponse->body(),
                        'status' => $uploadResponse->status(),
                    ]);
                    // Continue without logo instead of failing
                }
            } catch (\Exception $e) {
                \Log::warning('Team logo upload timeout or error, creating team without logo', [
                    'error' => $e->getMessage(),
                ]);
                // Continue without logo instead of failing
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
                'leader_id' => (int) $userId,
                'member_limit' => (int) $request->member_limit,
            ],
        ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            \Log::error('Failed to create team', [
                'response' => $response->body(),
                'status' => $response->status(),
                'errors' => $response->json()['errors'] ?? null,
            ]);
            return back()->with('error', 'Failed to create team: ' . ($response->json()['errors'][0]['message'] ?? 'Unknown error'));
        }

        // Get the created team_id from response
        $createdTeam = $response->json('data.insertIntoteamsCollection.records')[0] ?? null;
        
        if ($createdTeam && isset($createdTeam['team_id'])) {
            $teamId = $createdTeam['team_id'];
            
            // Insert leader as first member into team_members table
            $memberMutation = <<<'GRAPHQL'
            mutation InsertTeamMember($team_id: BigInt!, $user_id: BigInt!) {
                insertIntoteam_membersCollection(
                    objects: {
                        team_id: $team_id,
                        user_id: $user_id,
                        role: "leader",
                        status: "accepted",
                        joined_at: "now()"
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
                'query' => $memberMutation,
                'variables' => [
                    'team_id' => $teamId,
                    'user_id' => $userId,
                ],
            ]);
        }

        return redirect()->route('teams.index')->with('success', 'Team created successfully!');
    }

    public function update(Request $request, $team_id)
    {
        $request->validate([
            'team_desc' => 'nullable|string',
            'team_logo' => 'nullable|url',
            'member_limit' => 'required|integer|min:1|max:20',
        ]);

        $userId = session('user_id');
        if (!$userId) {
            return back()->with('error', 'Please login first');
        }

        // Verify user is team leader and get current member count
        $checkQuery = <<<'GRAPHQL'
        query CheckLeader($team_id: BigInt!, $userId: BigInt!) {
            teamsCollection(filter: { team_id: { eq: $team_id }, leader_id: { eq: $userId } }) {
                edges {
                    node {
                        team_id
                        member_count
                    }
                }
            }
        }
        GRAPHQL;

        $checkResponse = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $checkQuery,
            'variables' => [
                'team_id' => (int) $team_id,
                'userId' => (int) $userId
            ]
        ]);

        $teamEdges = $checkResponse->json('data.teamsCollection.edges');
        if (empty($teamEdges)) {
            return back()->with('error', 'Only team leader can edit team');
        }

        $team = $teamEdges[0]['node'];

        // Validate that new limit is not less than current member count
        if ($request->member_limit < $team['member_count']) {
            return back()->with('error', 'Cannot set member limit below current member count (' . $team['member_count'] . ')');
        }

        // Determine new status based on new limit
        $newStatus = ($team['member_count'] >= $request->member_limit) ? 'closed' : 'open';

        // Update team
        $mutation = <<<'GRAPHQL'
        mutation UpdateTeam($team_id: BigInt!, $team_desc: String, $team_logo: String, $member_limit: Int!, $team_status: String!) {
            updateteamsCollection(
                filter: { team_id: { eq: $team_id } }
                set: { 
                    team_desc: $team_desc,
                    team_logo: $team_logo,
                    member_limit: $member_limit,
                    team_status: $team_status
                }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $mutation,
            'variables' => [
                'team_id' => (int) $team_id,
                'team_desc' => $request->team_desc,
                'team_logo' => $request->team_logo,
                'member_limit' => (int) $request->member_limit,
                'team_status' => $newStatus
            ]
        ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            return back()->with('error', 'Failed to update team');
        }

        return back()->with('success', 'Team updated successfully!');
    }

    public function show($team_id)
    {
        $userId = session('user_id');

        // Query team detail
        $teamQuery = <<<'GRAPHQL'
        query GetTeam($team_id: BigInt!) {
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
            'query' => $teamQuery,
            'variables' => ['team_id' => (int) $team_id],
        ]);

        $teamEdges = $teamResponse->json('data.teamsCollection.edges') ?? [];
        if (empty($teamEdges)) {
            return redirect()->route('teams.index')->with('error', 'Team not found');
        }
        $team = $teamEdges[0]['node'];

        // Query team members
        $membersQuery = <<<'GRAPHQL'
        query GetMembers($team_id: BigInt!) {
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
            'query' => $membersQuery,
            'variables' => ['team_id' => (int) $team_id],
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
        $pendingQuery = <<<'GRAPHQL'
        query CheckPending($team_id: BigInt!, $userId: BigInt!) {
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
            'query' => $pendingQuery,
            'variables' => [
                'team_id' => (int) $team_id,
                'userId' => (int) $userId,
            ],
        ]);

        $isPending = !empty($pendingResponse->json('data.team_membersCollection.edges'));

        // Query pending members (only if user is leader)
        $pendingMembers = [];
        if ($team['leader_id'] == $userId) {
            $pendingMembersQuery = <<<'GRAPHQL'
            query GetPendingMembers($team_id: BigInt!) {
                team_membersCollection(filter: { team_id: { eq: $team_id }, status: { eq: "pending" } }) {
                    edges {
                        node {
                            member_id
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

            $pendingMembersResponse = Http::withHeaders([
                'apikey' => env('SUPABASE_ANON_KEY'),
                'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
                'Content-Type' => 'application/json'
            ])->post(env('SUPABASE_URL') . '/graphql/v1', [
                'query' => $pendingMembersQuery,
                'variables' => ['team_id' => (int) $team_id],
            ]);

            $pendingEdges = $pendingMembersResponse->json('data.team_membersCollection.edges') ?? [];
            $pendingMembers = array_map(function($edge) {
                return [
                    'member_id' => $edge['node']['member_id'],
                    'user_id' => $edge['node']['user_id'],
                    'name' => $edge['node']['users']['name'],
                    'avatar_url' => $edge['node']['users']['avatar_url'] ?? null,
                ];
            }, $pendingEdges);
        }

        // Auto-sync member_count dengan actual members count
        $actualMemberCount = count($members);
        if ($team['member_count'] != $actualMemberCount) {
            $newStatus = ($actualMemberCount >= $team['member_limit']) ? 'closed' : 'open';
            
            $syncMutation = <<<'GRAPHQL'
            mutation SyncMemberCount($team_id: BigInt!, $actualCount: Int!, $newStatus: String!) {
                updateteamsCollection(
                    filter: { team_id: { eq: $team_id } }
                    set: { member_count: $actualCount, team_status: $newStatus }
                ) {
                    affectedCount
                }
            }
            GRAPHQL;

            Http::withHeaders([
                'apikey' => env('SUPABASE_SERVICE_KEY'),
                'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
                'Content-Type' => 'application/json'
            ])->post(env('SUPABASE_URL') . '/graphql/v1', [
                'query' => $syncMutation,
                'variables' => [
                    'team_id' => (int) $team_id,
                    'actualCount' => $actualMemberCount,
                    'newStatus' => $newStatus
                ]
            ]);
            
            // Update local team data with synced values
            $team['member_count'] = $actualMemberCount;
            $team['team_status'] = $newStatus;
        }

        return view('pages.users.team-detail', compact('team', 'members', 'isMember', 'isPending', 'pendingMembers'));
    }

    public function join(Request $request, $team_id)
    {
        $userId = session('user_id');
        if (!$userId) {
            return back()->with('error', 'Please login first');
        }

        // Get team details including member count and limit
        $teamQuery = <<<'GRAPHQL'
        query GetTeamForJoin($team_id: BigInt!) {
            teamsCollection(filter: { team_id: { eq: $team_id } }) {
                edges {
                    node {
                        leader_id
                        team_name
                        member_count
                        member_limit
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
            'query' => $teamQuery,
            'variables' => ['team_id' => (int) $team_id],
        ]);

        $teamEdges = $teamResponse->json('data.teamsCollection.edges') ?? [];
        if (empty($teamEdges)) {
            return back()->with('error', 'Team not found');
        }
        $team = $teamEdges[0]['node'];

        // Check if team is full
        if ($team['member_count'] >= $team['member_limit']) {
            return back()->with('error', 'Team is full! Cannot send join request.');
        }

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

    public function destroy($team_id)
    {
        $userId = session('user_id');
        if (!$userId) {
            return back()->with('error', 'Please login first');
        }

        // Check if user is the leader
        $checkQuery = <<<'GRAPHQL'
        query CheckLeader($team_id: BigInt!, $userId: BigInt!) {
            teamsCollection(filter: { team_id: { eq: $team_id }, leader_id: { eq: $userId } }) {
                edges {
                    node {
                        team_id
                        team_logo
                    }
                }
            }
        }
        GRAPHQL;

        $checkResponse = Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
            'Content-Type' => 'application/json',
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $checkQuery,
            'variables' => [
                'team_id' => (int) $team_id,
                'userId' => (int) $userId,
            ],
        ]);

        $edges = $checkResponse->json('data.teamsCollection.edges') ?? [];
        if (empty($edges)) {
            return back()->with('error', 'You are not authorized to delete this team');
        }

        $team = $edges[0]['node'];

        // Delete team logo from storage if exists
        if (!empty($team['team_logo'])) {
            try {
                // Extract filename from URL
                $urlParts = parse_url($team['team_logo']);
                $path = $urlParts['path'] ?? '';
                // Remove /storage/v1/object/public/bucket-name/ prefix
                $fileName = preg_replace('#^/storage/v1/object/public/[^/]+/#', '', $path);
                
                if ($fileName) {
                    Http::timeout(10)
                        ->withHeaders([
                            'apikey' => env('SUPABASE_SERVICE_KEY'),
                            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
                        ])
                        ->delete(env('SUPABASE_URL') . '/storage/v1/object/post-images/' . $fileName);
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to delete team logo', ['error' => $e->getMessage()]);
            }
        }

        // Delete team members first
        $deleteMembersMutation = <<<'GRAPHQL'
        mutation DeleteTeamMembers($team_id: BigInt!) {
            deleteFromteam_membersCollection(filter: { team_id: { eq: $team_id } }) {
                affectedCount
            }
        }
        GRAPHQL;

        Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
            'Content-Type' => 'application/json',
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $deleteMembersMutation,
            'variables' => ['team_id' => (int) $team_id],
        ]);

        // Delete team
        $deleteTeamMutation = <<<'GRAPHQL'
        mutation DeleteTeam($team_id: BigInt!) {
            deleteFromteamsCollection(filter: { team_id: { eq: $team_id } }) {
                affectedCount
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
            'Content-Type' => 'application/json',
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $deleteTeamMutation,
            'variables' => ['team_id' => (int) $team_id],
        ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            \Log::error('Failed to delete team', [
                'response' => $response->body(),
                'errors' => $response->json()['errors'] ?? null,
            ]);
            return back()->with('error', 'Failed to delete team');
        }

        return redirect()->route('teams.index')->with('success', 'Team deleted successfully');
    }

    public function acceptMember($team_id, $user_id)
    {
        $leaderId = session('user_id');
        if (!$leaderId) {
            return back()->with('error', 'Please login first');
        }

        // Verify user is team leader and check member limit
        $checkQuery = <<<'GRAPHQL'
        query CheckLeaderAndLimit($team_id: BigInt!, $leaderId: BigInt!) {
            teamsCollection(filter: { team_id: { eq: $team_id }, leader_id: { eq: $leaderId } }) {
                edges {
                    node {
                        team_id
                        team_name
                        member_count
                        member_limit
                    }
                }
            }
        }
        GRAPHQL;

        $checkResponse = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $checkQuery,
            'variables' => [
                'team_id' => (int) $team_id,
                'leaderId' => (int) $leaderId
            ]
        ]);

        $teamEdges = $checkResponse->json('data.teamsCollection.edges');
        if (empty($teamEdges)) {
            return back()->with('error', 'Only team leader can accept members');
        }

        $team = $teamEdges[0]['node'];
        
        // Check if team is full
        if ($team['member_count'] >= $team['member_limit']) {
            return back()->with('error', 'Team is full! Cannot accept more members.');
        }

        // Update member status to accepted
        $mutation = <<<'GRAPHQL'
        mutation UpdateMember($team_id: BigInt!, $user_id: BigInt!) {
            updateteam_membersCollection(
                filter: { 
                    team_id: { eq: $team_id }
                    user_id: { eq: $user_id }
                    status: { eq: "pending" }
                }
                set: { status: "accepted" }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $mutation,
            'variables' => [
                'team_id' => (int) $team_id,
                'user_id' => (int) $user_id
            ]
        ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            return back()->with('error', 'Failed to accept member');
        }

        // Increment member_count di teams table
        $newCount = $team['member_count'] + 1;
        $newStatus = ($newCount >= $team['member_limit']) ? 'closed' : 'open';
        
        $incrementMutation = <<<'GRAPHQL'
        mutation IncrementMemberCount($team_id: BigInt!, $newCount: Int!, $newStatus: String!) {
            updateteamsCollection(
                filter: { team_id: { eq: $team_id } }
                set: { member_count: $newCount, team_status: $newStatus }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $incrementMutation,
            'variables' => [
                'team_id' => (int) $team_id,
                'newCount' => $newCount,
                'newStatus' => $newStatus
            ]
        ]);

        // Send notification to accepted user
        $leaderName = session('user_name');
        $notifMutation = <<<'GRAPHQL'
        mutation InsertNotification($user_id: BigInt!, $from_user_id: BigInt!, $team_id: BigInt!, $message: String!) {
            insertIntonotificationsCollection(
                objects: {
                    user_id: $user_id,
                    from_user_id: $from_user_id,
                    team_id: $team_id,
                    type: "team_accept",
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
                'user_id' => (int) $user_id,
                'from_user_id' => (int) $leaderId,
                'team_id' => (int) $team_id,
                'message' => "Your request to join {$team['team_name']} has been accepted!",
            ],
        ]);

        return back()->with('success', 'Member accepted successfully');
    }

    public function rejectMember($team_id, $user_id)
    {
        $leaderId = session('user_id');
        if (!$leaderId) {
            return back()->with('error', 'Please login first');
        }

        // Verify user is team leader
        $checkQuery = <<<'GRAPHQL'
        query CheckLeader($team_id: BigInt!, $leaderId: BigInt!) {
            teamsCollection(filter: { team_id: { eq: $team_id }, leader_id: { eq: $leaderId } }) {
                edges {
                    node {
                        team_id
                    }
                }
            }
        }
        GRAPHQL;

        $checkResponse = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $checkQuery,
            'variables' => [
                'team_id' => (int) $team_id,
                'leaderId' => (int) $leaderId
            ]
        ]);

        if (empty($checkResponse->json('data.teamsCollection.edges'))) {
            return back()->with('error', 'Only team leader can reject members');
        }

        // Delete pending member
        $mutation = <<<'GRAPHQL'
        mutation DeleteMember($team_id: BigInt!, $user_id: BigInt!) {
            deleteFromteam_membersCollection(
                filter: { 
                    team_id: { eq: $team_id }
                    user_id: { eq: $user_id }
                    status: { eq: "pending" }
                }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $mutation,
            'variables' => [
                'team_id' => (int) $team_id,
                'user_id' => (int) $user_id
            ]
        ]);

        return back()->with('success', 'Member rejected');
    }

    public function syncMemberCount($team_id)
    {
        // Count actual accepted members
        $countQuery = <<<'GRAPHQL'
        query CountMembers($team_id: BigInt!) {
            team_membersCollection(filter: { team_id: { eq: $team_id }, status: { eq: "accepted" } }) {
                edges {
                    node {
                        member_id
                    }
                }
            }
        }
        GRAPHQL;

        $countResponse = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $countQuery,
            'variables' => [
                'team_id' => (int) $team_id
            ]
        ]);

        $edges = $countResponse->json('data.team_membersCollection.edges') ?? [];
        $actualCount = count($edges);

        // Update teams.member_count
        $updateMutation = <<<'GRAPHQL'
        mutation UpdateMemberCount($team_id: BigInt!, $actualCount: Int!) {
            updateteamsCollection(
                filter: { team_id: { eq: $team_id } }
                set: { member_count: $actualCount }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $updateMutation,
            'variables' => [
                'team_id' => (int) $team_id,
                'actualCount' => $actualCount
            ]
        ]);

        return back()->with('success', "Member count synced: $actualCount members");
    }

    public function kickMember($team_id, $user_id)
    {
        $leaderId = session('user_id');
        if (!$leaderId) {
            return back()->with('error', 'Please login first');
        }

        // Verify user is team leader
        $checkQuery = <<<'GRAPHQL'
        query CheckLeader($team_id: BigInt!, $leaderId: BigInt!) {
            teamsCollection(filter: { team_id: { eq: $team_id }, leader_id: { eq: $leaderId } }) {
                edges {
                    node {
                        team_id
                        team_name
                        member_count
                    }
                }
            }
        }
        GRAPHQL;

        $checkResponse = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $checkQuery,
            'variables' => [
                'team_id' => (int) $team_id,
                'leaderId' => (int) $leaderId
            ]
        ]);

        $teamEdges = $checkResponse->json('data.teamsCollection.edges');
        if (empty($teamEdges)) {
            return back()->with('error', 'Only team leader can kick members');
        }

        $team = $teamEdges[0]['node'];

        // Prevent kicking self (leader)
        if ((int) $user_id === (int) $leaderId) {
            return back()->with('error', 'You cannot kick yourself');
        }

        // Delete member from team_members
        $mutation = <<<'GRAPHQL'
        mutation KickMember($team_id: BigInt!, $user_id: BigInt!) {
            deleteFromteam_membersCollection(
                filter: { 
                    team_id: { eq: $team_id }
                    user_id: { eq: $user_id }
                    status: { eq: "accepted" }
                }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $mutation,
            'variables' => [
                'team_id' => (int) $team_id,
                'user_id' => (int) $user_id
            ]
        ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            return back()->with('error', 'Failed to kick member');
        }

        // Auto-decrement member_count and update status
        $newCount = max(1, $team['member_count'] - 1);
        $newStatus = 'open'; // Always open after kicking since space is now available
        
        $decrementMutation = <<<'GRAPHQL'
        mutation DecrementMemberCount($team_id: BigInt!, $newCount: Int!, $newStatus: String!) {
            updateteamsCollection(
                filter: { team_id: { eq: $team_id } }
                set: { member_count: $newCount, team_status: $newStatus }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $decrementMutation,
            'variables' => [
                'team_id' => (int) $team_id,
                'newCount' => $newCount,
                'newStatus' => $newStatus
            ]
        ]);

        // Send notification to kicked user
        $leaderName = session('user_name');
        $notifMutation = <<<'GRAPHQL'
        mutation InsertNotification($user_id: BigInt!, $from_user_id: BigInt!, $team_id: BigInt!, $message: String!) {
            insertIntonotificationsCollection(
                objects: {
                    user_id: $user_id,
                    from_user_id: $from_user_id,
                    team_id: $team_id,
                    type: "team_kick",
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
                'user_id' => (int) $user_id,
                'from_user_id' => (int) $leaderId,
                'team_id' => (int) $team_id,
                'message' => "You have been removed from the team: {$team['team_name']}",
            ],
        ]);

        return back()->with('success', 'Member kicked successfully');
    }

    /**
     * Static helper method to fetch team recommendations
     * Can be called from any controller
     */
    public static function getTeamRecommendations($limit = 10)
    {
        $query = <<<'GRAPHQL'
        query GetTeams($limit: Int!) {
            teamsCollection(
                first: $limit
                orderBy: [{ member_count: DescNullsLast }]
            ) {
                edges {
                    node {
                        team_id
                        team_name
                        team_logo
                        team_status
                        member_count
                        member_limit
                    }
                }
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $query,
            'variables' => ['limit' => $limit]
        ]);

        if ($response->failed()) {
            return [];
        }

        $edges = $response->json('data.teamsCollection.edges') ?? [];
        return array_map(fn($edge) => $edge['node'], $edges);
    }

    // public function viewTeam()
    // {
    //     return view('pages.users.team');
    // }
 
}