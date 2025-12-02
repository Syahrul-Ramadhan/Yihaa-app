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
            materialsCollection(orderBy: { created_at: DescNullsLast }, first: 5) { 
                edges { 
                    node { 
                        material_id 
                        tittle 
                        description 
                        created_at 
                    } 
                } 
            }
            seminarCollection(orderBy: { created_at: DescNullsLast }, first: 3) {
                edges {
                    node {
                        seminar_id
                        nama_seminar
                        tanggal_pelaksanaan
                        created_at
                    }
                }
            }
            beasiswaCollection(orderBy: { created_at: DescNullsLast }, first: 3) {
                edges {
                    node {
                        beasiswa_id
                        nama_beasiswa
                        mulai_pendaftaran
                        created_at
                    }
                }
            }
            lombaCollection(orderBy: { created_at: DescNullsLast }, first: 3) {
                edges {
                    node {
                        lomba_id
                        nama_lomba
                        tanggal_pelaksanaan
                        created_at
                    }
                }
            }
            teamsCollection(orderBy: { created_at: DescNullsLast }, first: 10) { 
                edges { 
                    node { 
                        team_id 
                        team_name 
                        team_desc
                        member_count 
                    } 
                } 
            }
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
        
        // Combine all events
        $seminars = array_map(fn($edge) => $edge['node'], $data['seminarCollection']['edges'] ?? []);
        $beasiswas = array_map(fn($edge) => $edge['node'], $data['beasiswaCollection']['edges'] ?? []);
        $lombas = array_map(fn($edge) => $edge['node'], $data['lombaCollection']['edges'] ?? []);
        
        // Format events for display
        $events = [];
        foreach ($seminars as $seminar) {
            $events[] = [
                'nama' => 'Seminar',
                'tanggal' => $seminar['tanggal_pelaksanaan'] ?? $seminar['created_at'],
                'nama_event' => $seminar['nama_seminar'] ?? 'Seminar'
            ];
        }
        foreach ($beasiswas as $beasiswa) {
            $events[] = [
                'nama' => 'Beasiswa',
                'tanggal' => $beasiswa['mulai_pendaftaran'] ?? $beasiswa['created_at'],
                'nama_event' => $beasiswa['nama_beasiswa'] ?? 'Beasiswa'
            ];
        }
        foreach ($lombas as $lomba) {
            $events[] = [
                'nama' => 'Lomba',
                'tanggal' => $lomba['tanggal_pelaksanaan'] ?? $lomba['created_at'],
                'nama_event' => $lomba['nama_lomba'] ?? 'Lomba'
            ];
        }
        usort($events, fn($a, $b) => strtotime($b['tanggal']) - strtotime($a['tanggal']));
        $events = array_slice($events, 0, 5);
        
        $teams = array_map(fn($edge) => $edge['node'], $data['teamsCollection']['edges'] ?? []);

        return view('pages.admin.dashboard', [
            'usersCount' => count($users),
            'materiCount' => count($materials),
            'eventsCount' => count($seminars) + count($beasiswas) + count($lombas),
            'teamsCount' => count($teams),
            'materiList' => $materials,
            'eventsList' => $events,
            'teamsList' => $teams,
        ]);
    }
 
    public function viewManageEvent()
    {
        // Fetch all events
        $query = <<<'GRAPHQL'
        query {
            seminarCollection(orderBy: { created_at: DescNullsLast }) {
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
                        created_at
                    }
                }
            }
            beasiswaCollection(orderBy: { created_at: DescNullsLast }) {
                edges {
                    node {
                        beasiswa_id
                        nama_beasiswa
                        jenjang_beasiswa
                        mulai_pendaftaran
                        akhir_pendaftaran
                        syarat_beasiswa
                        benefit_beasiswa
                        pemberi_beasiswa
                        link_pendaftaran
                        created_at
                    }
                }
            }
            lombaCollection(orderBy: { created_at: DescNullsLast }) {
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

        $seminars = array_map(fn($edge) => $edge['node'], $data['seminarCollection']['edges'] ?? []);
        $beasiswas = array_map(fn($edge) => $edge['node'], $data['beasiswaCollection']['edges'] ?? []);
        $lombas = array_map(fn($edge) => $edge['node'], $data['lombaCollection']['edges'] ?? []);

        return view('pages.admin.manage-event', compact('seminars', 'beasiswas', 'lombas'));
    }

    public function viewManageMaterial()
    {
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

    public function viewManageUser()
    {
        $query = <<<'GRAPHQL'
        query {
            usersCollection(orderBy: { created_at: DescNullsLast }) {
                edges {
                    node {
                        id
                        name
                        email
                        password
                        role
                        avatar_url
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
        $users = array_map(fn($edge) => $edge['node'], $data['usersCollection']['edges'] ?? []);

        return view('pages.admin.manage-users', compact('users'));
    }
}