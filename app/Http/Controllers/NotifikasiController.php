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
        // Fetch notification untuk ambil from_user_id (user yang request join)
        $queryNotif = <<<GRAPHQL
        query {
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
            'query' => $queryNotif
        ]);

        $fromUserId = $notifResponse->json('data.notificationsCollection.edges.0.node.from_user_id');

        if (!$fromUserId) {
            return back()->with('error', 'Invalid notification');
        }

        // Update status team member jadi accepted (user yang request join)
        $mutation = <<<GRAPHQL
        mutation {
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

        Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json'
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $mutation
        ]);

        // Mark notification as read
        $this->markAsRead($notificationId);

        return back()->with('success', 'Team request accepted!');
    }
}