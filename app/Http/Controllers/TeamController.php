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
                        team_icon
                        team_status
                        team_origin
                        member_count
                        member_limit
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

    // public function viewTeam()
    // {
    //     return view('pages.users.team');
    // }
 
}