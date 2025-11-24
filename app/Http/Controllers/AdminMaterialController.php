<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AdminMaterialController extends Controller
{
    protected $supabaseUrl;
    protected $supabaseKey;

    public function __construct()
    {
        $this->supabaseUrl = env('SUPABASE_URL') . '/graphql/v1';
        $this->supabaseKey = env('SUPABASE_SERVICE_KEY'); // Use service key for admin operations
    }

    public function index()
    {
        // Fetch all materials with user info and filter by status (pending, approved, rejected)
        // Note: Assuming materials table has a 'status' field. If not, we'll fetch all and filter client-side
        $query = <<<'GRAPHQL'
        query {
            materialsCollection(orderBy: { created_at: DescNullsLast }) {
                edges {
                    node {
                        material_id
                        tittle
                        description
                        uploaded_by
                        file_url
                        thumbnail_url
                        status
                        created_at
                        users {
                            id
                            name
                            email
                            avatar_url
                        }
                    }
                }
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type' => 'application/json'
        ])->post($this->supabaseUrl, ['query' => $query]);

        $data = $response->json('data') ?? [];
        $materials = array_map(fn($edge) => $edge['node'], $data['materialsCollection']['edges'] ?? []);

        // Filter materials by status (if status field exists, otherwise treat all as pending)
        $pending = array_filter($materials, fn($m) => ($m['status'] ?? 'pending') === 'pending');
        $approved = array_filter($materials, fn($m) => ($m['status'] ?? 'pending') === 'approved');
        $rejected = array_filter($materials, fn($m) => ($m['status'] ?? 'pending') === 'rejected');

        return view('pages.admin.manage-materials', compact('pending', 'approved', 'rejected', 'materials'));
    }

    public function approve($id)
    {
        $mutation = <<<'GRAPHQL'
        mutation UpdateMaterialStatus($material_id: BigInt!, $status: String!) {
            updatematerialsCollection(
                filter: { material_id: { eq: $material_id } }
                set: { status: $status }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type' => 'application/json'
        ])->post($this->supabaseUrl, [
            'query' => $mutation,
            'variables' => [
                'material_id' => (int)$id,
                'status' => 'approved'
            ]
        ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            return back()->with('error', 'Failed to approve material: ' . ($response->json()['errors'][0]['message'] ?? 'Unknown error'));
        }

        return back()->with('success', 'Material approved successfully!');
    }

    public function reject($id)
    {
        $mutation = <<<'GRAPHQL'
        mutation UpdateMaterialStatus($material_id: BigInt!, $status: String!) {
            updatematerialsCollection(
                filter: { material_id: { eq: $material_id } }
                set: { status: $status }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type' => 'application/json'
        ])->post($this->supabaseUrl, [
            'query' => $mutation,
            'variables' => [
                'material_id' => (int)$id,
                'status' => 'rejected'
            ]
        ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            return back()->with('error', 'Failed to reject material: ' . ($response->json()['errors'][0]['message'] ?? 'Unknown error'));
        }

        return back()->with('success', 'Material rejected successfully!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tittle' => 'required|string|max:200',
            'description' => 'nullable|string',
            'file_url' => 'nullable|string|max:500',
            'thumbnail_url' => 'nullable|string|max:500',
            'status' => 'nullable|string|in:pending,approved,rejected',
        ]);

        $mutation = <<<'GRAPHQL'
        mutation InsertMaterial(
            $tittle: String!,
            $description: String,
            $file_url: String!,
            $thumbnail_url: String,
            $status: String
        ) {
            insertIntomaterialsCollection(
                objects: {
                    tittle: $tittle,
                    description: $description,
                    uploaded_by: 1,
                    file_url: $file_url,
                    thumbnail_url: $thumbnail_url,
                    status: $status
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

        $variables = $request->all();
        $variables['file_url'] = $variables['file_url'] ?? '';
        $variables['status'] = $variables['status'] ?? 'approved'; // Admin created materials are approved by default

        $response = Http::withHeaders([
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type' => 'application/json'
        ])->post($this->supabaseUrl, [
            'query' => $mutation,
            'variables' => $variables
        ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            return back()->with('error', 'Failed to create material: ' . ($response->json()['errors'][0]['message'] ?? 'Unknown error'));
        }

        return back()->with('success', 'Material created successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tittle' => 'required|string|max:200',
            'description' => 'nullable|string',
            'file_url' => 'nullable|string|max:500',
            'thumbnail_url' => 'nullable|string|max:500',
            'status' => 'nullable|string|in:pending,approved,rejected',
        ]);

        $mutation = <<<'GRAPHQL'
        mutation UpdateMaterial(
            $material_id: BigInt!,
            $tittle: String!,
            $description: String,
            $file_url: String,
            $thumbnail_url: String,
            $status: String
        ) {
            updatematerialsCollection(
                filter: { material_id: { eq: $material_id } }
                set: {
                    tittle: $tittle,
                    description: $description,
                    file_url: $file_url,
                    thumbnail_url: $thumbnail_url,
                    status: $status
                }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        $variables = $request->all();
        $variables['material_id'] = (int)$id;

        $response = Http::withHeaders([
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type' => 'application/json'
        ])->post($this->supabaseUrl, [
            'query' => $mutation,
            'variables' => $variables
        ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            return back()->with('error', 'Failed to update material: ' . ($response->json()['errors'][0]['message'] ?? 'Unknown error'));
        }

        return back()->with('success', 'Material updated successfully!');
    }

    public function destroy($id)
    {
        $mutation = <<<'GRAPHQL'
        mutation DeleteMaterial($material_id: BigInt!) {
            deleteFrommaterialsCollection(filter: { material_id: { eq: $material_id } }) {
                affectedCount
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type' => 'application/json'
        ])->post($this->supabaseUrl, [
            'query' => $mutation,
            'variables' => ['material_id' => (int)$id]
        ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            return back()->with('error', 'Failed to delete material: ' . ($response->json()['errors'][0]['message'] ?? 'Unknown error'));
        }

        return back()->with('success', 'Material deleted successfully!');
    }
}



