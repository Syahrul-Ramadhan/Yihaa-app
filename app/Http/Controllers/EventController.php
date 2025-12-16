<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EventController extends Controller
{
    /**
     * Show seminar listing page. We fetch seminar records from Supabase GraphQL.
     * Fields requested follow the schema you provided.
     */
    public function viewSeminar(Request $request)
    {
        $search = $request->input('search');
        $filter = null;

        if ($search) {
            $filter = [
                'nama_seminar' => [ 'ilike' => '%' . $search . '%' ]
            ];
        }

        // GraphQL query to fetch seminars
        $query = <<<'GRAPHQL'
        query ($filter: seminarFilter) {
            seminarCollection (
                filter: $filter,
                orderBy: [{ created_at: DescNullsLast }]
            ) {
                edges {
                    node {
                        seminar_id
                        nama_seminar
                        tanggal_pelaksanaan
                        mulai_pendaftaran
                        akhir_pendaftaran
                        lokasi
                        pembicara
                        deskripsi
                        link_pendaftaran
                        image_url
                        created_at
                    }
                }
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json',
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $query,
            'variables' => [
                'filter' => $filter
            ]
        ]);

        if ($response->failed()) {
            // If the request fails, return the view with an empty array and an error message
            return view('pages.events.seminar', ['seminars' => [], 'error' => 'Failed to fetch seminars']);
        }

        $edges = $response->json('data.seminarCollection.edges') ?? [];
        $seminars = array_map(fn($edge) => $edge['node'], $edges);

        // Get team recommendations
        $teams = \App\Http\Controllers\TeamController::getTeamRecommendations(20);

        return view('pages.events.seminar', compact('seminars', 'teams'));
    }

    /**
     * Show beasiswa listing page. Fetch beasiswa records from Supabase GraphQL.
     */
    public function viewBeasiswa(Request $request)
    {
        $search = $request->input('search');
        $filter = null;

        if ($search) {
            $filter = [
                'nama_beasiswa' => [ 'ilike' => '%' . $search . '%' ]
            ];
        }

        $query = <<<'GRAPHQL'
        query ($filter: beasiswaFilter) {
            beasiswaCollection (
                filter: $filter,
                orderBy: [{ created_at: DescNullsLast }]
            ) {
                edges {
                    node {
                        beasiswa_id
                        nama_beasiswa
                        jenjang_beasiswa
                        created_at
                        mulai_pendaftaran
                        akhir_pendaftaran
                        syarat_beasiswa
                        benefit_beasiswa
                        pemberi_beasiswa
                        link_pendaftaran
                        image_url
                    }
                }
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json',
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $query,
            'variables' => [
                'filter' => $filter
            ]
        ]);

        if ($response->failed()) {
            return view('pages.events.beasiswa', ['beasiswas' => [], 'error' => 'Failed to fetch beasiswa']);
        }

        $edges = $response->json('data.beasiswaCollection.edges') ?? [];
        $beasiswas = array_map(fn($edge) => $edge['node'], $edges);

        // Get team recommendations
        $teams = \App\Http\Controllers\TeamController::getTeamRecommendations(20);

        return view('pages.events.beasiswa', compact('beasiswas', 'teams'));
    }

    /**
     * Show lomba listing page. Fetch lomba records from Supabase GraphQL.
     */
    public function viewLomba(Request $request)
    {
        $search = $request->input('search');
        $filter = null;

        if ($search) {
            $filter = [
                'nama_lomba' => [ 'ilike' => '%' . $search . '%' ]
            ];
        }
        
        $query = <<<'GRAPHQL'
        query ($filter: lombaFilter) {
            lombaCollection (
                filter: $filter,
                orderBy: [{ created_at: DescNullsLast }]
            ){
                edges {
                    node {
                        lomba_id
                        nama_lomba
                        tanggal_pelaksanaan
                        mulai_pendaftaran
                        akhir_pendaftaran
                        lokasi
                        kategori_lomba
                        deskripsi
                        penyelenggara
                        link_pendaftaran
                        created_at
                        image_url
                    }
                }
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey' => env('SUPABASE_ANON_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_ANON_KEY'),
            'Content-Type' => 'application/json',
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $query,
            'variables' => [
                'filter' => $filter
            ]
        ]);

        if ($response->failed()) {
            return view('pages.events.lombas', ['lomba' => [], 'error' => 'Failed to fetch lomba']);
        }

        $edges = $response->json('data.lombaCollection.edges') ?? [];
        $lombas = array_map(fn($edge) => $edge['node'], $edges);

        // Get team recommendations
        $teams = \App\Http\Controllers\TeamController::getTeamRecommendations(20);

        return view('pages.events.lomba', compact('lombas', 'teams'));
    }
}