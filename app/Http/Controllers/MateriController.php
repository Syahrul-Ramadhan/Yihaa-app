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

    public function index(Request $request)
    {
        // Tangkap input pencarian
        $search = $request->input('search');

        $filter = null;
        if ($search) {
            $filter = [
                'or' => [
                    ['tittle' => ['ilike' => '%' . $search . '%']],
                    ['description' => ['ilike' => '%' . $search . '%']]
                ]
            ];
        }

        // Query GraphQL untuk ambil data materials
        $query = <<<'GRAPHQL'
        query($filter: materialsFilter) {
            materialsCollection(
                filter: $filter,
                orderBy: { material_id: DescNullsLast }
            ) {
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
            'variables' => [
                'filter' => $filter
            ]
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

        // Get team recommendations
        $teams = \App\Http\Controllers\TeamController::getTeamRecommendations(20);

        return view('pages.materi', compact('materials', 'teams'));
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

        $thumbnailUrl = '';

        // Upload thumbnail to Supabase Storage if provided (using 'post-images' bucket)
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $bucketName = 'post-images';
            $fileName = 'material_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Upload to Supabase Storage using existing 'post-images' bucket
            $uploadResponse = Http::withHeaders([
                'apikey' => env('SUPABASE_SERVICE_KEY'),
                'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
            ])
            ->withBody(
                $file->getContent(), 
                $file->getMimeType()
            )
            ->post(env('SUPABASE_URL') . '/storage/v1/object/' . $bucketName . '/' . $fileName);

            if ($uploadResponse->successful()) {
                $thumbnailUrl = env('SUPABASE_URL') . '/storage/v1/object/public/' . $bucketName . '/' . $fileName;
            } else {
                \Log::error('Failed to upload material image', [
                    'response' => $uploadResponse->body(),
                    'status' => $uploadResponse->status(),
                ]);
                return back()->with('error', 'Failed to upload image: ' . $uploadResponse->body());
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

        // file_url is NOT NULL in database, so use thumbnail or empty string
        $fileUrl = $thumbnailUrl ?: '';

        // If file_url is empty string, we need to handle it differently
        // Check if database actually allows empty string for file_url
        if (empty($fileUrl)) {
            $fileUrl = 'https://via.placeholder.com/1x1?text=';
        }

        $response = Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
            'Content-Type' => 'application/json',
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $mutation,
            'variables' => [
                'tittle' => $request->title,
                'description' => $request->description,
                'uploaded_by' => (int)$userId,
                'thumbnail_url' => $thumbnailUrl,
                'file_url' => $fileUrl,
            ],
        ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            \Log::error('Material creation failed', [
                'response' => $response->body(),
                'status' => $response->status(),
            ]);
            return back()->with('error', 'Failed to create material: ' . ($response->json()['errors'][0]['message'] ?? 'Unknown error'));
        }

        return redirect()->route('materi.index')->with('success', 'Material published successfully!');
    }

    public function destroy($material_id)
    {
        $userId = session('user_id');
        $userRole = session('user_role');

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Please login first'], 401);
        }

        // Query material untuk validasi ownership
        $query = <<<'GRAPHQL'
        query GetMaterial($material_id: BigInt!) {
            materialsCollection(filter: { material_id: { eq: $material_id } }) {
                edges {
                    node {
                        material_id
                        uploaded_by
                        thumbnail_url
                        file_url
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
            'variables' => ['material_id' => (int)$material_id],
        ]);

        $edges = $response->json('data.materialsCollection.edges') ?? [];
        
        if (empty($edges)) {
            return response()->json(['success' => false, 'message' => 'Material not found'], 404);
        }

        $material = $edges[0]['node'];

        // Validasi: hanya pemilik atau admin yang bisa delete
        if ($material['uploaded_by'] != $userId && $userRole !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Delete material dari database
        $mutation = <<<'GRAPHQL'
        mutation DeleteMaterial($material_id: BigInt!) {
            deleteFrommaterialsCollection(filter: { material_id: { eq: $material_id } }) {
                affectedCount
            }
        }
        GRAPHQL;

        $deleteResponse = Http::withHeaders([
            'apikey' => env('SUPABASE_SERVICE_KEY'),
            'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
            'Content-Type' => 'application/json',
        ])->post(env('SUPABASE_URL') . '/graphql/v1', [
            'query' => $mutation,
            'variables' => ['material_id' => (int)$material_id],
        ]);

        if ($deleteResponse->failed() || isset($deleteResponse->json()['errors'])) {
            return response()->json(['success' => false, 'message' => 'Failed to delete material'], 500);
        }

        // Delete image dari storage jika ada dan bukan placeholder
        if (!empty($material['thumbnail_url']) && !str_contains($material['thumbnail_url'], 'placeholder')) {
            $imagePath = basename(parse_url($material['thumbnail_url'], PHP_URL_PATH));
            
            Http::withHeaders([
                'apikey' => env('SUPABASE_SERVICE_KEY'),
                'Authorization' => 'Bearer ' . env('SUPABASE_SERVICE_KEY'),
            ])->delete(env('SUPABASE_URL') . '/storage/v1/object/post-images/' . $imagePath);
        }

        return response()->json(['success' => true, 'message' => 'Material deleted successfully']);
    }
}