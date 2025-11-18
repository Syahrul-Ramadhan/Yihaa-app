<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NotifikasiController extends Controller
{
    public function index()
    {
        $userId = session('user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        // Query untuk ambil notifikasi user
        $query = <<<GRAPHQL
        query {
            notificationsCollection(
                filter: { user_id: { eq: $userId } }
                orderBy: { created_at: DescNullsLast }
            ) {
                edges {
                    node {
                        notification_id
                        user_id
                        from_user_id
                        team_id
                        type
                        message
                        is_read
                        created_at
                        users {
                            name
                            avatar_url
                        }
                        teams {
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
            'query' => $query
        ]);

        if ($response->failed()) {
            return view('pages.notifikasi', ['notifications' => []]);
        }

        $edges = $response->json('data.notificationsCollection.edges') ?? [];
        $notifications = array_map(fn($edge) => $edge['node'], $edges);

        return view('pages.notifikasi', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $mutation = <<<GRAPHQL
        mutation {
            updatenotificationsCollection(
                filter: { notification_id: { eq: $id } }
                set: { is_read: true }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $mutation
        ]);

        return back();
    }

    public function markAllAsRead()
    {
        $userId = session('user_id');

        $mutation = <<<GRAPHQL
        mutation {
            updatenotificationsCollection(
                filter: { user_id: { eq: $userId } }
                set: { is_read: true }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $mutation
        ]);

        return back()->with('success', 'All notifications marked as read');
    }

    public function delete($id)
    {
        $mutation = <<<GRAPHQL
        mutation {
            deleteFromnotificationsCollection(
                filter: { notification_id: { eq: $id } }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $mutation
        ]);

        return back()->with('success', 'Notification deleted');
    }

    public function acceptTeamRequest($notificationId, $teamId)
    {
        // Check team member limit first and get team name
        $teamQuery = <<<'GRAPHQL'
        query GetTeamLimit($teamId: BigInt!) {
            teamsCollection(filter: { team_id: { eq: $teamId } }) {
                edges {
                    node {
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
            'variables' => [
                'teamId' => (int) $teamId
            ]
        ]);

        $teamEdges = $teamResponse->json('data.teamsCollection.edges');
        if (!empty($teamEdges)) {
            $team = $teamEdges[0]['node'];
            if ($team['member_count'] >= $team['member_limit']) {
                return back()->with('error', 'Team is full! Cannot accept more members.');
            }
        }

        // Fetch notification untuk ambil from_user_id (user yang request join)
        $queryNotif = <<<'GRAPHQL'
        query GetNotification($notificationId: BigInt!) {
            notificationsCollection(
                filter: { notification_id: { eq: $notificationId } }
            ) {
                edges {
                    node {
                        from_user_id
                    }
                }
            }
        }
        GRAPHQL;

        $notifResponse = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $queryNotif,
            'variables' => [
                'notificationId' => (int) $notificationId
            ]
        ]);

        $fromUserId = $notifResponse->json('data.notificationsCollection.edges.0.node.from_user_id');

        if (!$fromUserId) {
            return back()->with('error', 'Invalid notification');
        }

        // Update status team member jadi accepted (user yang request join)
        $mutation = <<<'GRAPHQL'
        mutation UpdateTeamMember($teamId: BigInt!, $fromUserId: BigInt!) {
            updateteam_membersCollection(
                filter: { 
                    team_id: { eq: $teamId }
                    user_id: { eq: $fromUserId }
                    status: { eq: "pending" }
                }
                set: { status: "accepted" }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        $updateResponse = Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $mutation,
            'variables' => [
                'teamId' => (int) $teamId,
                'fromUserId' => (int) $fromUserId
            ]
        ]);

        if ($updateResponse->failed() || isset($updateResponse->json()['errors'])) {
            return back()->with('error', 'Failed to accept team request');
        }

        // Increment member_count and update status di teams table
        $newCount = $team['member_count'] + 1;
        $newStatus = ($newCount >= $team['member_limit']) ? 'closed' : 'open';
        
        $incrementMutation = <<<'GRAPHQL'
        mutation IncrementMemberCount($teamId: BigInt!, $newCount: Int!, $newStatus: String!) {
            updateteamsCollection(
                filter: { team_id: { eq: $teamId } }
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
                'teamId' => (int) $teamId,
                'newCount' => $newCount,
                'newStatus' => $newStatus
            ]
        ]);

        // Send notification to accepted user
        $leaderId = session('user_id');
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
                'user_id' => (int) $fromUserId,
                'from_user_id' => (int) $leaderId,
                'team_id' => (int) $teamId,
                'message' => "Your request to join {$team['team_name']} has been accepted!",
            ],
        ]);

        // Mark notification as read
        $this->markAsRead($notificationId);

        return back()->with('success', 'Team request accepted!');
    }
    
    public function rejectTeamRequest($notificationId, $teamId)
    {
        // Fetch notification untuk ambil from_user_id
        $queryNotif = <<<'GRAPHQL'
        query GetNotification($notificationId: BigInt!) {
            notificationsCollection(
                filter: { notification_id: { eq: $notificationId } }
            ) {
                edges {
                    node {
                        from_user_id
                    }
                }
            }
        }
        GRAPHQL;

        $notifResponse = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $queryNotif,
            'variables' => [
                'notificationId' => (int) $notificationId
            ]
        ]);

        $fromUserId = $notifResponse->json('data.notificationsCollection.edges.0.node.from_user_id');

        if (!$fromUserId) {
            return back()->with('error', 'Invalid notification');
        }

        // Delete team member request (reject)
        $mutation = <<<'GRAPHQL'
        mutation DeleteTeamMember($teamId: BigInt!, $fromUserId: BigInt!) {
            deleteFromteam_membersCollection(
                filter: { 
                    team_id: { eq: $teamId }
                    user_id: { eq: $fromUserId }
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
                'teamId' => (int) $teamId,
                'fromUserId' => (int) $fromUserId
            ]
        ]);

        // Delete notification
        $this->delete($notificationId);

        return back()->with('success', 'Team request rejected');
    }
}