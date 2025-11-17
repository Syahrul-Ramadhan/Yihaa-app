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

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $userId = session('user_id');
        if (!$userId) {
            return back()->with('error', 'Please login first');
        }

        $thumbnailUrl = null;

        // Upload thumbnail to Supabase Storage if provided
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = 'material_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $fileContent = file_get_contents($file->getRealPath());

            // Upload to Supabase Storage
            $uploadResponse = Http::withHeaders([
                'apikey' => env('SUPABASE_SERVICE_KEY'),
                'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
                'Content-Type' => $file->getMimeType(),
            ])->send('POST', env('SUPABASE_URL') . '/storage/v1/object/materials/' . $fileName, [
                'body' => $fileContent
            ]);

            if ($uploadResponse->successful()) {
                $thumbnailUrl = env('SUPABASE_URL') . '/storage/v1/object/public/materials/' . $fileName;
            }
        }

        // Insert material via GraphQL
        $mutation = <<<'GRAPHQL'
        mutation InsertMaterial($tittle: String!, $description: String!, $uploaded_by: BigInt!, $thumbnail_url: String, $file_url: String) {
            insertIntomaterialsCollection(
                objects: {
                    tittle: $tittle,
                    description: $description,
                    uploaded_by: $uploaded_by,
                    thumbnail_url: $thumbnail_url,
                    file_url: $file_url
                }
            ) {
                affectedCount
                records {
                    material_id
                    tittle
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
                'tittle' => $request->title,
                'description' => $request->description,
                'uploaded_by' => $userId,
                'thumbnail_url' => $thumbnailUrl,
                'file_url' => $thumbnailUrl, // Same as thumbnail for now
            ],
        ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            return back()->with('error', 'Failed to create material');
        }

        return redirect()->route('materi.index')->with('success', 'Material published successfully!');
    }
}