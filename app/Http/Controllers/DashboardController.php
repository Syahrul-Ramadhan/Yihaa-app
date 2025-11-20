<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $supabaseUrl;
    protected $supabaseKey;

    public function __construct()
    {
        $this->supabaseUrl = env('SUPABASE_URL') . '/graphql/v1';
        $this->supabaseKey = env('SUPABASE_ANON_KEY');
    }

    public function viewDashboard()
    {
        $query = <<<'GRAPHQL'
        query {
            usersCollection { edges { node { id name email } } }
            materialsCollection { edges { node { materi_id title description created_at } } }
            lombaCollection { edges { node { id nama tanggal lokasi } } }
            teamsCollection { edges { node { team_id team_name member_count } } }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type' => 'application/json'
        ])->post($this->supabaseUrl, [ 'query' => $query ]);

        $data = $response->json('data') ?? [];

        $users = array_map(fn($edge) => $edge['node'], $data['usersCollection']['edges'] ?? []);
        $materials = array_map(fn($edge) => $edge['node'], $data['materialsCollection']['edges'] ?? []);
        $events = array_map(fn($edge) => $edge['node'], $data['lombaCollection']['edges'] ?? []);
        $teams = array_map(fn($edge) => $edge['node'], $data['teamsCollection']['edges'] ?? []);

        return view('pages.admin.dashboard', [
            'usersCount' => count($users),
            'materiCount' => count($materials),
            'eventsCount' => count($events),
            'teamsCount' => count($teams),
            'materiList' => $materials,
            'eventsList' => $events,
            'teamsList' => $teams,
        ]);
    }
 
    public function viewManageEvent()
    {
        return view('pages.admin.manage-event');
    }
}