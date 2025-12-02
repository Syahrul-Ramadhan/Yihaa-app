<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function index()
    {
        $userId = session('user_id');
        Log::info('Profile Index - User ID from session:', ['user_id' => $userId]);

        if (!$userId) {
            return redirect()->route('login');
        }

        // Get user data with profile
        $query = <<<'GRAPHQL'
        query GetUserProfile($userId: BigInt!) {
            usersCollection(filter: { id: { eq: $userId } }) {
                edges {
                    node {
                        id
                        name
                        email
                        avatar_url
                    }
                }
            }
            profilesCollection(filter: { user_id: { eq: $userId } }) {
                edges {
                    node {
                        profile_id
                        phone
                        university
                        program_study
                        user_type
                        bio
                        role_in_team
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
            'variables' => ['userId' => (int) $userId]
        ]);

        Log::info('Profile Index - GraphQL Response:', [
            'status' => $response->status(),
            'body' => $response->json()
        ]);

        if ($response->failed()) {
            Log::error('Profile Index - Response failed');
            return back()->with('error', 'Failed to load profile');
        }

        $userEdges = $response->json('data.usersCollection.edges');
        Log::info('Profile Index - User Edges:', ['edges' => $userEdges]);

        if (empty($userEdges)) {
            Log::warning('Profile Index - User not found for ID: ' . $userId);
            // Show debug info
            dd([
                'user_id' => $userId,
                'query' => $query,
                'response' => $response->json(),
                'userEdges' => $userEdges
            ]);
            return back()->with('error', 'User not found');
        }

        $user = $userEdges[0]['node'];

        // Get profile separately
        $profileEdges = $response->json('data.profilesCollection.edges');
        $profile = !empty($profileEdges) ? $profileEdges[0]['node'] : null;

        // Get activity counts
        $activities = $this->getUserActivities($userId);

        // Get contributions
        $contributions = $this->getUserContributions($userId);

        // Get team collaborations
        $teams = $this->getUserTeams($userId);

        return view('pages.users.profile', compact('user', 'profile', 'activities', 'contributions', 'teams'));
    }

    private function getUserActivities($userId)
    {
        // Count posts
        $postsQuery = <<<'GRAPHQL'
        query CountPosts($userId: BigInt!) {
            postsCollection(filter: { uploaded_by: { eq: $userId } }) {
                edges {
                    node {
                        post_id
                    }
                }
            }
        }
        GRAPHQL;

        $postsResponse = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $postsQuery,
            'variables' => ['userId' => (int) $userId]
        ]);

        $postsCount = count($postsResponse->json('data.postsCollection.edges') ?? []);

        // Count materials
        $materialsQuery = <<<'GRAPHQL'
        query CountMaterials($userId: BigInt!) {
            materialsCollection(filter: { uploaded_by: { eq: $userId } }) {
                edges {
                    node {
                        material_id
                    }
                }
            }
        }
        GRAPHQL;

        $materialsResponse = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $materialsQuery,
            'variables' => ['userId' => (int) $userId]
        ]);

        $materialsCount = count($materialsResponse->json('data.materialsCollection.edges') ?? []);

        // Count events
        $eventsQuery = <<<'GRAPHQL'
        query CountEvents($userId: BigInt!) {
            user_eventsCollection(filter: { user_id: { eq: $userId } }) {
                edges {
                    node {
                        user_event_id
                    }
                }
            }
        }
        GRAPHQL;

        $eventsResponse = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $eventsQuery,
            'variables' => ['userId' => (int) $userId]
        ]);

        $eventsCount = count($eventsResponse->json('data.user_eventsCollection.edges') ?? []);

        // Count teams
        $teamsQuery = <<<'GRAPHQL'
        query CountTeams($userId: BigInt!) {
            team_membersCollection(filter: { user_id: { eq: $userId }, status: { eq: "accepted" } }) {
                edges {
                    node {
                        member_id
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
            'query' => $teamsQuery,
            'variables' => ['userId' => (int) $userId]
        ]);

        $teamsCount = count($teamsResponse->json('data.team_membersCollection.edges') ?? []);

        return [
            'posts' => $postsCount,
            'materials' => $materialsCount,
            'events' => $eventsCount,
            'teams' => $teamsCount
        ];
    }

    private function getUserContributions($userId)
    {
        $query = <<<'GRAPHQL'
        query GetContributions($userId: BigInt!) {
            user_contributionsCollection(
                filter: { user_id: { eq: $userId } }
                orderBy: { contribution_date: DescNullsLast }
                first: 2
            ) {
                edges {
                    node {
                        contribution_id
                        title
                        description
                        contribution_date
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
            'variables' => ['userId' => (int) $userId]
        ]);

        $edges = $response->json('data.user_contributionsCollection.edges') ?? [];
        return array_map(fn($edge) => $edge['node'], $edges);
    }

    private function getUserTeams($userId)
    {
        $query = <<<'GRAPHQL'
        query GetUserTeams($userId: BigInt!) {
            team_membersCollection(
                filter: { user_id: { eq: $userId }, status: { eq: "accepted" } }
                first: 3
            ) {
                edges {
                    node {
                        member_id
                        role
                        teams {
                            team_id
                            team_name
                            team_logo
                        }
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
            'variables' => ['userId' => (int) $userId]
        ]);

        $edges = $response->json('data.team_membersCollection.edges') ?? [];
        return array_map(function ($edge) {
            return [
                'team_id' => $edge['node']['teams']['team_id'],
                'team_name' => $edge['node']['teams']['team_name'],
                'team_logo' => $edge['node']['teams']['team_logo'],
                'role' => $edge['node']['role']
            ];
        }, $edges);
    }

    public function showEdit()
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        // Get user data with profile
        $query = <<<'GRAPHQL'
        query GetUserProfile($userId: BigInt!) {
            usersCollection(filter: { id: { eq: $userId } }) {
                edges {
                    node {
                        id
                        name
                        email
                        avatar_url
                    }
                }
            }
            profilesCollection(filter: { user_id: { eq: $userId } }) {
                edges {
                    node {
                        profile_id
                        phone
                        university
                        program_study
                        user_type
                        bio
                        role_in_team
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
            'variables' => ['userId' => (int) $userId]
        ]);

        if ($response->failed()) {
            return back()->with('error', 'Failed to load profile');
        }

        $userEdges = $response->json('data.usersCollection.edges');
        if (empty($userEdges)) {
            return back()->with('error', 'User not found');
        }

        $user = $userEdges[0]['node'];

        // Get profile separately
        $profileEdges = $response->json('data.profilesCollection.edges');
        $profile = !empty($profileEdges) ? $profileEdges[0]['node'] : null;

        return view('pages.users.profile-edit', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'university' => 'nullable|string|max:255',
            'program_study' => 'nullable|string|max:255',
            'user_type' => 'required|in:mahasiswa,dosen,alumni',
            'bio' => 'nullable|string',
            'role_in_team' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $userId = session('user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $avatarUrl = null;

        // Upload avatar if provided
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $bucketName = 'post-images';
            $fileName = 'avatar_' . $userId . '_' . time() . '.' . $file->getClientOriginalExtension();

            try {
                $uploadResponse = Http::timeout(30)
                    ->withHeaders([
                        'apikey' => env('SUPABASE_SERVICE_KEY'),
                        'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
                    ])
                    ->attach('file', fopen($file->getRealPath(), 'r'), $fileName)
                    ->post(env('SUPABASE_URL') . '/storage/v1/object/' . $bucketName . '/' . $fileName);

                if ($uploadResponse->successful()) {
                    $avatarUrl = env('SUPABASE_URL') . '/storage/v1/object/public/' . $bucketName . '/' . $fileName;
                }
            } catch (\Exception $e) {
                Log::warning('Avatar upload error: ' . $e->getMessage());
            }
        }

        // Update user name and avatar
        $updateUserMutation = <<<'GRAPHQL'
        mutation UpdateUser($userId: BigInt!, $name: String!, $avatar_url: String) {
            updateusersCollection(
                filter: { id: { eq: $userId } }
                set: { name: $name, avatar_url: $avatar_url }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        $userVars = [
            'userId' => (int) $userId,
            'name' => $request->name
        ];

        if ($avatarUrl) {
            $userVars['avatar_url'] = $avatarUrl;
            session(['avatar_url' => $avatarUrl]); // Update session
        }

        Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $updateUserMutation,
            'variables' => $userVars
        ]);

        // Update session name
        session(['user_name' => $request->name]);

        // Update or create profile
        $updateProfileMutation = <<<'GRAPHQL'
        mutation UpdateProfile(
            $userId: BigInt!,
            $phone: String,
            $university: String,
            $program_study: String,
            $user_type: String!,
            $bio: String,
            $role_in_team: String
        ) {
            updateprofilesCollection(
                filter: { user_id: { eq: $userId } }
                set: {
                    phone: $phone,
                    university: $university,
                    program_study: $program_study,
                    user_type: $user_type,
                    bio: $bio,
                    role_in_team: $role_in_team,
                    updated_at: "now()"
                }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        $profileResponse = Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $updateProfileMutation,
            'variables' => [
                'userId' => (int) $userId,
                'phone' => $request->phone,
                'university' => $request->university,
                'program_study' => $request->program_study,
                'user_type' => $request->user_type,
                'bio' => $request->bio,
                'role_in_team' => $request->role_in_team
            ]
        ]);

        // If no profile exists, create one
        if ($profileResponse->json('data.updateprofilesCollection.affectedCount') == 0) {
            $createProfileMutation = <<<'GRAPHQL'
            mutation CreateProfile(
                $userId: BigInt!,
                $phone: String,
                $university: String,
                $program_study: String,
                $user_type: String!,
                $bio: String,
                $role_in_team: String
            ) {
                insertIntoprofilesCollection(
                    objects: {
                        user_id: $userId,
                        phone: $phone,
                        university: $university,
                        program_study: $program_study,
                        user_type: $user_type,
                        bio: $bio,
                        role_in_team: $role_in_team
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
                'query' => $createProfileMutation,
                'variables' => [
                    'userId' => (int) $userId,
                    'phone' => $request->phone,
                    'university' => $request->university,
                    'program_study' => $request->program_study,
                    'user_type' => $request->user_type,
                    'bio' => $request->bio,
                    'role_in_team' => $request->role_in_team
                ]
            ]);
        }

        return redirect()->route('profile.index')->with('success', 'Profile updated successfully!');
    }
}
