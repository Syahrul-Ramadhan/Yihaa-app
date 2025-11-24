<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AdminEventController extends Controller
{
    protected $supabaseUrl;
    protected $supabaseKey;

    public function __construct()
    {
        $this->supabaseUrl = env('SUPABASE_URL') . '/graphql/v1';
        $this->supabaseKey = env('SUPABASE_SERVICE_KEY'); // Use service key for admin operations
    }

    // SEMINAR CRUD
    public function storeSeminar(Request $request)
    {
        $request->validate([
            'nama_seminar' => 'required|string|max:200',
            'tanggal_pelaksanaan' => 'required|date',
            'mulai_pendaftaran' => 'required|date',
            'akhir_pendaftaran' => 'required|date',
            'lokasi' => 'nullable|string|max:255',
            'pembicara' => 'nullable|string|max:200',
            'deskripsi' => 'nullable|string',
            'link_pendaftaran' => 'nullable|string|max:500',
        ]);

        $mutation = <<<'GRAPHQL'
        mutation InsertSeminar(
            $nama_seminar: String!,
            $tanggal_pelaksanaan: Date!,
            $mulai_pendaftaran: Date!,
            $akhir_pendaftaran: Date!,
            $lokasi: String,
            $pembicara: String,
            $deskripsi: String,
            $link_pendaftaran: String
        ) {
            insertIntoseminarCollection(
                objects: {
                    nama_seminar: $nama_seminar,
                    tanggal_pelaksanaan: $tanggal_pelaksanaan,
                    mulai_pendaftaran: $mulai_pendaftaran,
                    akhir_pendaftaran: $akhir_pendaftaran,
                    lokasi: $lokasi,
                    pembicara: $pembicara,
                    deskripsi: $deskripsi,
                    link_pendaftaran: $link_pendaftaran
                }
            ) {
                affectedCount
                records {
                    seminar_id
                    nama_seminar
                }
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type' => 'application/json'
        ])->post($this->supabaseUrl, [
            'query' => $mutation,
            'variables' => $request->all()
        ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            return back()->with('error', 'Failed to create seminar: ' . ($response->json()['errors'][0]['message'] ?? 'Unknown error'));
        }

        return back()->with('success', 'Seminar created successfully!');
    }

    public function updateSeminar(Request $request, $id)
    {
        $request->validate([
            'nama_seminar' => 'required|string|max:200',
            'tanggal_pelaksanaan' => 'required|date',
            'mulai_pendaftaran' => 'required|date',
            'akhir_pendaftaran' => 'required|date',
            'lokasi' => 'nullable|string|max:255',
            'pembicara' => 'nullable|string|max:200',
            'deskripsi' => 'nullable|string',
            'link_pendaftaran' => 'nullable|string|max:500',
        ]);

        $mutation = <<<'GRAPHQL'
        mutation UpdateSeminar(
            $seminar_id: BigInt!,
            $nama_seminar: String!,
            $tanggal_pelaksanaan: Date!,
            $mulai_pendaftaran: Date!,
            $akhir_pendaftaran: Date!,
            $lokasi: String,
            $pembicara: String,
            $deskripsi: String,
            $link_pendaftaran: String
        ) {
            updateseminarCollection(
                filter: { seminar_id: { eq: $seminar_id } }
                set: {
                    nama_seminar: $nama_seminar,
                    tanggal_pelaksanaan: $tanggal_pelaksanaan,
                    mulai_pendaftaran: $mulai_pendaftaran,
                    akhir_pendaftaran: $akhir_pendaftaran,
                    lokasi: $lokasi,
                    pembicara: $pembicara,
                    deskripsi: $deskripsi,
                    link_pendaftaran: $link_pendaftaran
                }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        $variables = $request->all();
        $variables['seminar_id'] = (int)$id;

        $response = Http::withHeaders([
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type' => 'application/json'
        ])->post($this->supabaseUrl, [
            'query' => $mutation,
            'variables' => $variables
        ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            return back()->with('error', 'Failed to update seminar: ' . ($response->json()['errors'][0]['message'] ?? 'Unknown error'));
        }

        return back()->with('success', 'Seminar updated successfully!');
    }

    public function deleteSeminar($id)
    {
        $mutation = <<<'GRAPHQL'
        mutation DeleteSeminar($seminar_id: BigInt!) {
            deleteFromseminarCollection(filter: { seminar_id: { eq: $seminar_id } }) {
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
            'variables' => ['seminar_id' => (int)$id]
        ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            return back()->with('error', 'Failed to delete seminar: ' . ($response->json()['errors'][0]['message'] ?? 'Unknown error'));
        }

        return back()->with('success', 'Seminar deleted successfully!');
    }

    // BEASISWA CRUD
    public function storeBeasiswa(Request $request)
    {
        $request->validate([
            'nama_beasiswa' => 'required|string|max:200',
            'jenjang_beasiswa' => 'nullable|string|max:100',
            'mulai_pendaftaran' => 'required|date',
            'akhir_pendaftaran' => 'required|date',
            'syarat_beasiswa' => 'nullable|string',
            'benefit_beasiswa' => 'nullable|string',
            'pemberi_beasiswa' => 'nullable|string|max:200',
            'link_pendaftaran' => 'nullable|string|max:500',
        ]);

        $mutation = <<<'GRAPHQL'
        mutation InsertBeasiswa(
            $nama_beasiswa: String!,
            $jenjang_beasiswa: String,
            $mulai_pendaftaran: Date!,
            $akhir_pendaftaran: Date!,
            $syarat_beasiswa: String,
            $benefit_beasiswa: String,
            $pemberi_beasiswa: String,
            $link_pendaftaran: String
        ) {
            insertIntobeasiswaCollection(
                objects: {
                    nama_beasiswa: $nama_beasiswa,
                    jenjang_beasiswa: $jenjang_beasiswa,
                    mulai_pendaftaran: $mulai_pendaftaran,
                    akhir_pendaftaran: $akhir_pendaftaran,
                    syarat_beasiswa: $syarat_beasiswa,
                    benefit_beasiswa: $benefit_beasiswa,
                    pemberi_beasiswa: $pemberi_beasiswa,
                    link_pendaftaran: $link_pendaftaran
                }
            ) {
                affectedCount
                records {
                    beasiswa_id
                    nama_beasiswa
                }
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type' => 'application/json'
        ])->post($this->supabaseUrl, [
            'query' => $mutation,
            'variables' => $request->all()
        ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            return back()->with('error', 'Failed to create beasiswa: ' . ($response->json()['errors'][0]['message'] ?? 'Unknown error'));
        }

        return back()->with('success', 'Beasiswa created successfully!');
    }

    public function updateBeasiswa(Request $request, $id)
    {
        $request->validate([
            'nama_beasiswa' => 'required|string|max:200',
            'jenjang_beasiswa' => 'nullable|string|max:100',
            'mulai_pendaftaran' => 'required|date',
            'akhir_pendaftaran' => 'required|date',
            'syarat_beasiswa' => 'nullable|string',
            'benefit_beasiswa' => 'nullable|string',
            'pemberi_beasiswa' => 'nullable|string|max:200',
            'link_pendaftaran' => 'nullable|string|max:500',
        ]);

        $mutation = <<<'GRAPHQL'
        mutation UpdateBeasiswa(
            $beasiswa_id: BigInt!,
            $nama_beasiswa: String!,
            $jenjang_beasiswa: String,
            $mulai_pendaftaran: Date!,
            $akhir_pendaftaran: Date!,
            $syarat_beasiswa: String,
            $benefit_beasiswa: String,
            $pemberi_beasiswa: String,
            $link_pendaftaran: String
        ) {
            updatebeasiswaCollection(
                filter: { beasiswa_id: { eq: $beasiswa_id } }
                set: {
                    nama_beasiswa: $nama_beasiswa,
                    jenjang_beasiswa: $jenjang_beasiswa,
                    mulai_pendaftaran: $mulai_pendaftaran,
                    akhir_pendaftaran: $akhir_pendaftaran,
                    syarat_beasiswa: $syarat_beasiswa,
                    benefit_beasiswa: $benefit_beasiswa,
                    pemberi_beasiswa: $pemberi_beasiswa,
                    link_pendaftaran: $link_pendaftaran
                }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        $variables = $request->all();
        $variables['beasiswa_id'] = (int)$id;

        $response = Http::withHeaders([
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type' => 'application/json'
        ])->post($this->supabaseUrl, [
            'query' => $mutation,
            'variables' => $variables
        ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            return back()->with('error', 'Failed to update beasiswa: ' . ($response->json()['errors'][0]['message'] ?? 'Unknown error'));
        }

        return back()->with('success', 'Beasiswa updated successfully!');
    }

    public function deleteBeasiswa($id)
    {
        $mutation = <<<'GRAPHQL'
        mutation DeleteBeasiswa($beasiswa_id: BigInt!) {
            deleteFrombeasiswaCollection(filter: { beasiswa_id: { eq: $beasiswa_id } }) {
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
            'variables' => ['beasiswa_id' => (int)$id]
        ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            return back()->with('error', 'Failed to delete beasiswa: ' . ($response->json()['errors'][0]['message'] ?? 'Unknown error'));
        }

        return back()->with('success', 'Beasiswa deleted successfully!');
    }

    // LOMBA CRUD
    public function storeLomba(Request $request)
    {
        $request->validate([
            'nama_lomba' => 'required|string|max:200',
            'tanggal_pelaksanaan' => 'required|date',
            'mulai_pendaftaran' => 'required|date',
            'akhir_pendaftaran' => 'required|date',
            'lokasi' => 'nullable|string|max:255',
            'kategori_lomba' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'penyelenggara' => 'nullable|string|max:200',
            'link_pendaftaran' => 'nullable|string|max:500',
        ]);

        $mutation = <<<'GRAPHQL'
        mutation InsertLomba(
            $nama_lomba: String!,
            $tanggal_pelaksanaan: Date!,
            $mulai_pendaftaran: Date!,
            $akhir_pendaftaran: Date!,
            $lokasi: String,
            $kategori_lomba: String,
            $deskripsi: String,
            $penyelenggara: String,
            $link_pendaftaran: String
        ) {
            insertIntolombaCollection(
                objects: {
                    nama_lomba: $nama_lomba,
                    tanggal_pelaksanaan: $tanggal_pelaksanaan,
                    mulai_pendaftaran: $mulai_pendaftaran,
                    akhir_pendaftaran: $akhir_pendaftaran,
                    lokasi: $lokasi,
                    kategori_lomba: $kategori_lomba,
                    deskripsi: $deskripsi,
                    penyelenggara: $penyelenggara,
                    link_pendaftaran: $link_pendaftaran
                }
            ) {
                affectedCount
                records {
                    lomba_id
                    nama_lomba
                }
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type' => 'application/json'
        ])->post($this->supabaseUrl, [
            'query' => $mutation,
            'variables' => $request->all()
        ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            return back()->with('error', 'Failed to create lomba: ' . ($response->json()['errors'][0]['message'] ?? 'Unknown error'));
        }

        return back()->with('success', 'Lomba created successfully!');
    }

    public function updateLomba(Request $request, $id)
    {
        $request->validate([
            'nama_lomba' => 'required|string|max:200',
            'tanggal_pelaksanaan' => 'required|date',
            'mulai_pendaftaran' => 'required|date',
            'akhir_pendaftaran' => 'required|date',
            'lokasi' => 'nullable|string|max:255',
            'kategori_lomba' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'penyelenggara' => 'nullable|string|max:200',
            'link_pendaftaran' => 'nullable|string|max:500',
        ]);

        $mutation = <<<'GRAPHQL'
        mutation UpdateLomba(
            $lomba_id: BigInt!,
            $nama_lomba: String!,
            $tanggal_pelaksanaan: Date!,
            $mulai_pendaftaran: Date!,
            $akhir_pendaftaran: Date!,
            $lokasi: String,
            $kategori_lomba: String,
            $deskripsi: String,
            $penyelenggara: String,
            $link_pendaftaran: String
        ) {
            updatelombaCollection(
                filter: { lomba_id: { eq: $lomba_id } }
                set: {
                    nama_lomba: $nama_lomba,
                    tanggal_pelaksanaan: $tanggal_pelaksanaan,
                    mulai_pendaftaran: $mulai_pendaftaran,
                    akhir_pendaftaran: $akhir_pendaftaran,
                    lokasi: $lokasi,
                    kategori_lomba: $kategori_lomba,
                    deskripsi: $deskripsi,
                    penyelenggara: $penyelenggara,
                    link_pendaftaran: $link_pendaftaran
                }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        $variables = $request->all();
        $variables['lomba_id'] = (int)$id;

        $response = Http::withHeaders([
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type' => 'application/json'
        ])->post($this->supabaseUrl, [
            'query' => $mutation,
            'variables' => $variables
        ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            return back()->with('error', 'Failed to update lomba: ' . ($response->json()['errors'][0]['message'] ?? 'Unknown error'));
        }

        return back()->with('success', 'Lomba updated successfully!');
    }

    public function deleteLomba($id)
    {
        $mutation = <<<'GRAPHQL'
        mutation DeleteLomba($lomba_id: BigInt!) {
            deleteFromlombaCollection(filter: { lomba_id: { eq: $lomba_id } }) {
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
            'variables' => ['lomba_id' => (int)$id]
        ]);

        if ($response->failed() || isset($response->json()['errors'])) {
            return back()->with('error', 'Failed to delete lomba: ' . ($response->json()['errors'][0]['message'] ?? 'Unknown error'));
        }

        return back()->with('success', 'Lomba deleted successfully!');
    }
}



