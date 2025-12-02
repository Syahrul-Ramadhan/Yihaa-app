<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MateriController extends Controller
{
    protected $supabaseUrl;
    protected $supabaseKey;
    private $supabaseStorage;

    public function __construct()
    {
        $this->supabaseUrl = env('SUPABASE_URL') . '/graphql/v1';
        $this->supabaseKey = env('SUPABASE_SERVICE_KEY'); // Use service key for admin operations
        $this->supabaseStorage = env('SUPABASE_URL') . '/storage/v1/object';
    }
    
    public function viewMateri()
    {
        return view('pages.materi');
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        // Filter dasar: hanya approved
        $filter = [
            'status' => ['eq' => 'approved']
        ];

        // Jika ada pencarian, gabungkan filter
        if ($search) {
            $filter = [
                'and' => [
                    ['status' => ['eq' => 'approved']],
                    [
                        'or' => [
                            ['tittle' => ['ilike' => '%' . $search . '%']],
                            ['description' => ['ilike' => '%' . $search . '%']]
                        ]
                    ]
                ]
            ];
        }

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
                        status
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

        $edges = $response->json('data.materialsCollection.edges') ?? [];

        $materials = collect($edges)->map(function ($edge) {
            $node = $edge['node'];
            return [
                'material_id'    => $node['material_id'],
                'tittle'         => $node['tittle'],
                'description'    => $node['description'],
                'uploaded_by'    => $node['uploaded_by'],
                'file_url'       => $node['file_url'],
                'thumbnail_url'  => $node['thumbnail_url'],
                'user'           => $node['users'],
            ];
        });

        $teams = \App\Http\Controllers\TeamController::getTeamRecommendations(20);

        return view('pages.materi', compact('materials', 'teams'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'tittle' => 'required|string|max:200',
            'description' => 'nullable|string',
            'file' => 'nullable|file',
            'image' => 'nullable|image',
        ]);

        $userId = session('user_id');
        if (!$userId) {
            return back()->with('error', 'Please login first.');
        }

        $imageUrl = null;
        $fileUrl  = null;


        /* ================================
        UPLOAD IMAGE (material_images)
        ================================= */
        if ($request->hasFile('image')) {

            $bucketName = 'material_images';

            $file = $request->file('image');
            $fileName = 'material_' . time() . '.' . $file->getClientOriginalExtension();
            $fileContent = file_get_contents($file);

            $upload = Http::withHeaders([
                'apikey'        => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
            ])
            ->attach('file', $fileContent, $fileName)
            ->post($this->supabaseStorage . "/{$bucketName}/{$fileName}");

            if ($upload->failed()) {
                return dd($upload->json());
            }

            // URL public
            $imageUrl = $this->supabaseStorage . "/{$bucketName}/{$fileName}";
        }


        /* ================================
        UPLOAD FILE (material_files)
        ================================= */
        if ($request->hasFile('file')) {

            $bucketName = 'material_files';

            $file = $request->file('file');
            $fileName = 'file_' . time() . '.' . $file->getClientOriginalExtension();
            $fileContent = file_get_contents($file);

            $upload = Http::withHeaders([
                'apikey'        => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
            ])
            ->attach('file', $fileContent, $fileName)
            ->post($this->supabaseStorage . "/{$bucketName}/{$fileName}");

            if ($upload->failed()) {
                return dd($upload->json());
            }

            $fileUrl = $this->supabaseStorage . "/{$bucketName}/{$fileName}";
        }


        /* ================================
        INSERT MATERIAL VIA GRAPHQL
        ================================= */
        $query = <<<'GRAPHQL'
        mutation InsertMaterial(
            $tittle: String!,
            $description: String!,
            $file: String,
            $image: String,
            $uploaded_by: BigInt!
        ) {
            insertIntomaterialsCollection(
                objects: {
                    tittle: $tittle,
                    description: $description,
                    uploaded_by: $uploaded_by,
                    file_url: $file,
                    thumbnail_url: $image,
                    status: "pending"
                }
            ) {
                affectedCount
                records {
                    material_id
                }
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type' => 'application/json'
        ])
        ->post($this->supabaseUrl, [
            'query' => $query,
            'variables' => [
                'tittle'       => $request->tittle,
                'description'  => $request->description ?? '',
                'file'         => $fileUrl,
                'image'        => $imageUrl,
                'uploaded_by'  => (int)$userId,
            ]
        ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            return back()->with(
                'error',
                'Failed to create material: ' .
                ($response->json()['errors'][0]['message'] ?? 'Unknown error')
            );
        }

        return back()->with('success', 'Materi berhasil diajukan, menunggu acc admin!');
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