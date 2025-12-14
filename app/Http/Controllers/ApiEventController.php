<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiEventController extends Controller
{
    protected $supabaseUrl;
    protected $supabaseKey;

    public function __construct()
    {
        $this->supabaseUrl = env('SUPABASE_URL') . '/graphql/v1';
        $this->supabaseKey = env('SUPABASE_SERVICE_KEY'); // Use service key for admin operations
    }

    // SEMINAR CRUD
    public function apiStoreSeminar(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama'                => 'required|string|max:255',
            'tanggal_pelaksanaan' => 'required|date',
            'mulai_pendaftaran'   => 'required|date',
            'akhir_pendaftaran'   => 'required|date',
            'lokasi'              => 'required|string|max:255',
            'pembicara'           => 'required|string|max:255',
            'deskripsi'           => 'required|string',
            'link'                => 'required|string',
        ]);

        // Ambil semua input
        $data = [
            'nama'                 => $request->nama,
            'tanggalPelaksanaan'   => $request->tanggal_pelaksanaan,
            'mulaiPendaftaran'     => $request->mulai_pendaftaran,
            'akhirPendaftaran'     => $request->akhir_pendaftaran,
            'lokasi'               => $request->lokasi,
            'pembicara'            => $request->pembicara,
            'deskripsi'            => $request->deskripsi,
            'link'                 => $request->link,
        ];

        // GraphQL Mutation
        $query = <<<GRAPHQL
        mutation InsertSeminar(
            \$nama: String!,
            \$tanggalPelaksanaan: Date!,
            \$mulaiPendaftaran: Date!,
            \$akhirPendaftaran: Date!,
            \$lokasi: String!,
            \$pembicara: String!,
            \$deskripsi: String!,
            \$link: String!,
            \$gambar: String
        ) {
            insertIntoseminarCollection(
                objects: {
                    nama_seminar: \$nama,
                    tanggal_pelaksanaan: \$tanggalPelaksanaan,
                    mulai_pendaftaran: \$mulaiPendaftaran,
                    akhir_pendaftaran: \$akhirPendaftaran,
                    lokasi: \$lokasi,
                    pembicara: \$pembicara,
                    deskripsi: \$deskripsi,
                    link_pendaftaran: \$link,
                    image_url: \$gambar
                }
            ) {
                records {
                    seminar_id
                    nama_seminar
                }
            }
        }
        GRAPHQL;

        // Kirim request ke Supabase
        $response = Http::withHeaders([
            'apikey'        => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type'  => 'application/json',
        ])->post($this->supabaseUrl, [
            'query'     => $query,
            'variables' => $data
        ]);

        $dataResponse = $response->json();

        if (!isset($dataResponse['data']['insertIntoseminarCollection'])) {
            return response()->json([
                'success' => false,
                'error' => 'Supabase tidak memberi response data',
                'raw' => $dataResponse
            ], 500);
        }

        $insertResult = $dataResponse['data']['insertIntoseminarCollection'];

        // Jika records tidak ada → pakai affectedCount saja
        if (!isset($insertResult['records'])) {
            return response()->json([
                'success' => true,
                'message' => 'Seminar berhasil dibuat!',
                'data' => [
                    'affectedCount' => $insertResult['affectedCount']
                ]
            ], 201);
        }

        return response()->json([
            'success' => true,
            'message' => 'Seminar berhasil dibuat!',
            'data' => $insertResult['records'][0]
        ], 201);
    }

    public function apiUpdateSeminar(Request $request, $id)
    {
        // Validasi
        $request->validate([
            'nama' => 'required|string|max:255',
            'tanggal_pelaksanaan' => 'required|date',
            'mulai_pendaftaran' => 'required|date',
            'akhir_pendaftaran' => 'required|date',
            'lokasi' => 'required|string|max:255',
            'pembicara' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'link' => 'required|string',
            'gambar' => 'nullable|string'
        ]);

        // dd($request->all(), $request->getContent());

        $data = [
            'id' => (int) $id,
            'nama' => $request->nama,
            'tanggalPelaksanaan' => $request->tanggal_pelaksanaan,
            'mulaiPendaftaran' => $request->mulai_pendaftaran,
            'akhirPendaftaran' => $request->akhir_pendaftaran,
            'lokasi' => $request->lokasi,
            'pembicara' => $request->pembicara,
            'deskripsi' => $request->deskripsi,
            'link' => $request->link,
            'gambar' => $request->gambar
        ];

        // GraphQL Mutation
        $query = <<<GRAPHQL
        mutation UpdateSeminar(
            \$id: Int!,
            \$nama: String!,
            \$tanggalPelaksanaan: Date!,
            \$mulaiPendaftaran: Date!,
            \$akhirPendaftaran: Date!,
            \$lokasi: String!,
            \$pembicara: String!,
            \$deskripsi: String!,
            \$link: String!,
            \$gambar: String
        ) {
            updateseminarCollection(
                filter: {seminar_id: {eq: \$id}},
                set: {
                    nama_seminar: \$nama,
                    tanggal_pelaksanaan: \$tanggalPelaksanaan,
                    mulai_pendaftaran: \$mulaiPendaftaran,
                    akhir_pendaftaran: \$akhirPendaftaran,
                    lokasi: \$lokasi,
                    pembicara: \$pembicara,
                    deskripsi: \$deskripsi,
                    link_pendaftaran: \$link,
                    image_url: \$gambar
                }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type' => 'application/json',
        ])->post($this->supabaseUrl, [
            'query' => $query,
            'variables' => $data
        ]);

        return response()->json($response->json(), 200);
    }

    public function apiDeleteSeminar($id)
    {
        $query = <<<GRAPHQL
        mutation DeleteSeminar(\$id: Int!) {
            deleteFromseminarCollection(
                filter: {seminar_id: {eq: \$id}}
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type' => 'application/json',
        ])->post($this->supabaseUrl, [
            'query' => $query,
            'variables' => [ 'id' => $id ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Seminar berhasil dihapus!',
            'response' => $response->json()
        ], 200);
    }

    // LOMBA
    public function apiStoreLomba(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'tanggal_pelaksanaan' => 'required|date',
            'mulai_pendaftaran' => 'required|date',
            'akhir_pendaftaran' => 'required|date',
            'lokasi' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'penyelenggara' => 'required|string|max:255',
            'link' => 'required|string',
            'gambar' => 'nullable|string'
        ]);

        $data = [
            'nama' => $request->nama,
            'tanggalPelaksanaan' => $request->tanggal_pelaksanaan,
            'mulaiPendaftaran' => $request->mulai_pendaftaran,
            'akhirPendaftaran' => $request->akhir_pendaftaran,
            'lokasi' => $request->lokasi,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
            'penyelenggara' => $request->penyelenggara,
            'link' => $request->link,
            'gambar' => $request->gambar
        ];

        $query = <<<GRAPHQL
        mutation InsertLomba(
            \$nama: String!,
            \$tanggalPelaksanaan: Date!,
            \$mulaiPendaftaran: Date!,
            \$akhirPendaftaran: Date!,
            \$lokasi: String!,
            \$kategori: String!,
            \$deskripsi: String!,
            \$penyelenggara: String!,
            \$link: String!,
            \$gambar: String
        ) {
            insertIntolombaCollection(
                objects: {
                    nama_lomba: \$nama,
                    tanggal_pelaksanaan: \$tanggalPelaksanaan,
                    mulai_pendaftaran: \$mulaiPendaftaran,
                    akhir_pendaftaran: \$akhirPendaftaran,
                    lokasi: \$lokasi,
                    kategori_lomba: \$kategori,
                    deskripsi: \$deskripsi,
                    penyelenggara: \$penyelenggara,
                    link_pendaftaran: \$link,
                    image_url: \$gambar
                }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type' => 'application/json',
        ])->post($this->supabaseUrl, [
            'query' => $query,
            'variables' => $data
        ]);

        return response()->json($response->json(), 201);
    }

    public function apiUpdateLomba(Request $request, $id)
{
    $request->validate([
        'nama' => 'required|string|max:255',
        'tanggal_pelaksanaan' => 'required|date',
        'mulai_pendaftaran' => 'required|date',
        'akhir_pendaftaran' => 'required|date',
        'lokasi' => 'required|string|max:255',
        'kategori' => 'required|string|max:255',
        'deskripsi' => 'required|string',
        'penyelenggara' => 'required|string|max:255',
        'link' => 'required|string',
        'gambar' => 'nullable|string'
    ]);

    $data = [
        'id' => (int) $id,
        'nama' => $request->nama,
        'tanggalPelaksanaan' => $request->tanggal_pelaksanaan,
        'mulaiPendaftaran' => $request->mulai_pendaftaran,
        'akhirPendaftaran' => $request->akhir_pendaftaran,
        'lokasi' => $request->lokasi,
        'kategori' => $request->kategori,
        'deskripsi' => $request->deskripsi,
        'penyelenggara' => $request->penyelenggara,
        'link' => $request->link,
        'gambar' => $request->gambar
    ];

    $query = <<<GRAPHQL
    mutation UpdateLomba(
        \$id: Int!,
        \$nama: String!,
        \$tanggalPelaksanaan: Date!,
        \$mulaiPendaftaran: Date!,
        \$akhirPendaftaran: Date!,
        \$lokasi: String!,
        \$kategori: String!,
        \$deskripsi: String!,
        \$penyelenggara: String!,
        \$link: String!,
        \$gambar: String
    ) {
        updatelombaCollection(
            filter: {lomba_id: {eq: \$id}},
            set: {
                nama_lomba: \$nama,
                tanggal_pelaksanaan: \$tanggalPelaksanaan,
                mulai_pendaftaran: \$mulaiPendaftaran,
                akhir_pendaftaran: \$akhirPendaftaran,
                lokasi: \$lokasi,
                kategori_lomba: \$kategori,
                deskripsi: \$deskripsi,
                penyelenggara: \$penyelenggara,
                link_pendaftaran: \$link,
                image_url: \$gambar
            }
        ) {
            affectedCount
        }
    }
    GRAPHQL;

    $response = Http::withHeaders([
        'apikey' => $this->supabaseKey,
        'Authorization' => 'Bearer ' . $this->supabaseKey,
        'Content-Type' => 'application/json',
    ])->post($this->supabaseUrl, [
        'query' => $query,
        'variables' => $data
    ]);

    return response()->json($response->json(), 200);
}

public function apiDeleteLomba($id)
{
    $query = <<<GRAPHQL
    mutation DeleteLomba(\$id: Int!) {
        deleteFromlombaCollection(
            filter: {lomba_id: {eq: \$id}}
        ) {
            affectedCount
        }
    }
    GRAPHQL;

    $response = Http::withHeaders([
        'apikey' => $this->supabaseKey,
        'Authorization' => 'Bearer ' . $this->supabaseKey,
        'Content-Type' => 'application/json',
    ])->post($this->supabaseUrl, [
        'query' => $query,
        'variables' => ['id' => (int) $id]
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Lomba berhasil dihapus!',
        'response' => $response->json()
    ], 200);
}

public function apiStoreBeasiswa(Request $request)
{
    $request->validate([
        'nama' => 'required|string|max:255',
        'jenjang_beasiswa' => 'required|string|max:255',
        'mulai_pendaftaran' => 'required|date',
        'akhir_pendaftaran' => 'required|date',
        'syarat' => 'required|string',
        'benefit' => 'required|string',
        'pemberi' => 'required|string|max:255',
        'link' => 'required|string',
        'gambar' => 'nullable|string'
    ]);

    $data = [
        'nama' => $request->nama,
        'jenjangBeasiswa' => $request->jenjang_beasiswa,
        'mulaiPendaftaran' => $request->mulai_pendaftaran,
        'akhirPendaftaran' => $request->akhir_pendaftaran,
        'syarat' => $request->syarat,
        'benefit' => $request->benefit,
        'pemberi' => $request->pemberi,
        'link' => $request->link,
        'gambar' => $request->gambar
    ];

    $query = <<<GRAPHQL
    mutation InsertBeasiswa(
        \$nama: String!,
        \$jenjangBeasiswa: String!,
        \$mulaiPendaftaran: Date!,
        \$akhirPendaftaran: Date!,
        \$syarat: String!,
        \$benefit: String!,
        \$pemberi: String!,
        \$link: String!,
        \$gambar: String
    ) {
        insertIntobeasiswaCollection(
            objects: {
                nama_beasiswa: \$nama,
                jenjang_beasiswa: \$jenjangBeasiswa,
                mulai_pendaftaran: \$mulaiPendaftaran,
                akhir_pendaftaran: \$akhirPendaftaran,
                syarat_beasiswa: \$syarat,
                benefit_beasiswa: \$benefit,
                pemberi_beasiswa: \$pemberi,
                link_pendaftaran: \$link,
                image_url: \$gambar
            }
        ) {
            affectedCount
        }
    }
    GRAPHQL;

    $response = Http::withHeaders([
        'apikey' => $this->supabaseKey,
        'Authorization' => 'Bearer ' . $this->supabaseKey,
        'Content-Type' => 'application/json',
    ])->post($this->supabaseUrl, [
        'query' => $query,
        'variables' => $data
    ]);

    return response()->json($response->json(), 201);
}

public function apiUpdateBeasiswa(Request $request, $id)
{
    $request->validate([
        'nama' => 'required|string|max:255',
        'jenjang_beasiswa' => 'required|string|max:255',
        'mulai_pendaftaran' => 'required|date',
        'akhir_pendaftaran' => 'required|date',
        'syarat' => 'required|string',
        'benefit' => 'required|string',
        'pemberi' => 'required|string|max:255',
        'link' => 'required|string',
        'gambar' => 'nullable|string'
    ]);

    $data = [
        'id' => (int) $id,
        'nama' => $request->nama,
        'jenjangBeasiswa' => $request->jenjang_beasiswa,
        'mulaiPendaftaran' => $request->mulai_pendaftaran,
        'akhirPendaftaran' => $request->akhir_pendaftaran,
        'syarat' => $request->syarat,
        'benefit' => $request->benefit,
        'pemberi' => $request->pemberi,
        'link' => $request->link,
        'gambar' => $request->gambar
    ];

    $query = <<<GRAPHQL
    mutation UpdateBeasiswa(
        \$id: Int!,
        \$nama: String!,
        \$jenjangBeasiswa: String!,
        \$mulaiPendaftaran: Date!,
        \$akhirPendaftaran: Date!,
        \$syarat: String!,
        \$benefit: String!,
        \$pemberi: String!,
        \$link: String!,
        \$gambar: String
    ) {
        updatebeasiswaCollection(
            filter: {beasiswa_id: {eq: \$id}},
            set: {
                nama_beasiswa: \$nama,
                jenjang_beasiswa: \$jenjangBeasiswa,
                mulai_pendaftaran: \$mulaiPendaftaran,
                akhir_pendaftaran: \$akhirPendaftaran,
                syarat_beasiswa: \$syarat,
                benefit_beasiswa: \$benefit,
                pemberi_beasiswa: \$pemberi,
                link_pendaftaran: \$link,
                image_url: \$gambar
            }
        ) {
            affectedCount
        }
    }
    GRAPHQL;

    $response = Http::withHeaders([
        'apikey' => $this->supabaseKey,
        'Authorization' => 'Bearer ' . $this->supabaseKey,
        'Content-Type' => 'application/json',
    ])->post($this->supabaseUrl, [
        'query' => $query,
        'variables' => $data
    ]);

    return response()->json($response->json(), 200);
}

public function apiDeleteBeasiswa($id)
{
    $query = <<<GRAPHQL
    mutation DeleteBeasiswa(\$id: Int!) {
        deleteFrombeasiswaCollection(
            filter: {beasiswa_id: {eq: \$id}}
        ) {
            affectedCount
        }
    }
    GRAPHQL;

    $response = Http::withHeaders([
        'apikey' => $this->supabaseKey,
        'Authorization' => 'Bearer ' . $this->supabaseKey,
        'Content-Type' => 'application/json',
    ])->post($this->supabaseUrl, [
        'query' => $query,
        'variables' => ['id' => (int) $id]
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Beasiswa berhasil dihapus!',
        'response' => $response->json()
    ], 200);
}

}