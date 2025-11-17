<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MateriController extends Controller
{
    public function viewMateri()
    {
        return view('pages.materi');
    }

    public function index()
    {
        // Query GraphQL untuk ambil data teams
        $query = <<<'GRAPHQL'
        query {
            materialsCollection {
                edges {
                    node {
                    material_id
                    tittle
                    description
                    uploaded_by
                    file_url
                    thumbnail_url
                    users {
                        name
                        avatar_url
                    }
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
        ]);

        if ($response->failed()) {
            dd('Error GraphQL: ' . $response->body());
        }

        
        // Ambil node data
        $edges = $response->json('data.materialsCollection.edges') ?? [];

        // Ubah ke bentuk array Laravel-friendly
        $materials = collect($edges)->map(function ($edge) {
        $node = $edge['node'];
        return [
            'material_id' => $node['material_id'],
            'tittle' => $node['tittle'],
            'description' => $node['description'],
            'uploaded_by' => $node['uploaded_by'],
            'file_url' => $node['file_url'],
            'thumbnail_url' => $node['thumbnail_url'],
            'user' => $node['users'],
        ];
    });

        return view('pages.materi', compact('materials'));
    }
}