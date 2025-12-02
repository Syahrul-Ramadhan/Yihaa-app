<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AdminEventController extends Controller
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

    // SEMINAR CRUD
    public function storeSeminar(Request $request)
    {
        // dd($request->all());

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
            'gambar'              => 'nullable|image|max:2048',
        ]);

        $imageUrl = null;

        //upload ke supabase storage jika ada gambar
        if ($request->hasFile('gambar')) {

            $bucketName = 'seminar-images';

            $file = $request->file('gambar');
            $fileName = 'seminar_' . time() . '.' . $file->getClientOriginalExtension();
            $fileContent = file_get_contents($file);

            // Upload ke Supabase Storage
            $upload = Http::withHeaders([
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
            ])
            ->attach(
                'file',                       // field name
                $fileContent,                 // isi file
                $fileName                     // nama file
            )
            ->post($this->supabaseStorage . '/seminar-images/' . $fileName);

            if ($upload->failed()) {
                return dd($upload->json());
            }

            // URL file
            $imageUrl = $this->supabaseStorage . '/' . $bucketName . '/' . $fileName;
        }

        // Ambil data request
        $nama                = $request->nama;
        $tanggalPelaksanaan  = $request->tanggal_pelaksanaan;
        $mulaiPendaftaran    = $request->mulai_pendaftaran;
        $akhirPendaftaran    = $request->akhir_pendaftaran;
        $lokasi              = $request->lokasi;
        $pembicara           = $request->pembicara;
        $deskripsi           = $request->deskripsi;
        $link                = $request->link;

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
                affectedCount
            }
        }
        GRAPHQL;

        // Kirim ke Supabase
        $response = Http::withHeaders([
            'apikey'        => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type'  => 'application/json'
        ])->post($this->supabaseUrl, [
            'query' => $query,
            'variables' => [
                'nama'                => $nama,
                'tanggalPelaksanaan'  => $tanggalPelaksanaan,
                'mulaiPendaftaran'    => $mulaiPendaftaran,
                'akhirPendaftaran'    => $akhirPendaftaran,
                'lokasi'              => $lokasi,
                'pembicara'           => $pembicara,
                'deskripsi'           => $deskripsi,
                'link'                => $link,
                'gambar'              => $imageUrl ?? '',
            ]
        ]);

        // Jika error
        if ($response->failed()) {
            return dd($response->json());
        }

        return back()->with('success', 'Seminar berhasil ditambahkan!');
    }

    public function updateSeminar(Request $request)
    {
        // Validasi
        $request->validate([
            'id'                  => 'required|integer',
            'nama'                => 'required|string|max:255',
            'tanggal_pelaksanaan' => 'required|date',
            'mulai_pendaftaran'   => 'required|date',
            'akhir_pendaftaran'   => 'required|date',
            'lokasi'              => 'required|string|max:255',
            'pembicara'           => 'required|string|max:255',
            'deskripsi'           => 'required|string',
            'link'                => 'required|url',
            'gambar'              => 'nullable|image|max:2048', // <--- validasi gambar
            'old_image'           => 'nullable|string',         // <--- gambar lama
            
        ]);

        // Ambil variabel
        $id                 = $request->id;
        $nama               = $request->nama;
        $tanggalPelaksanaan = $request->tanggal_pelaksanaan;
        $mulaiPendaftaran   = $request->mulai_pendaftaran;
        $akhirPendaftaran   = $request->akhir_pendaftaran;
        $lokasi             = $request->lokasi;
        $pembicara          = $request->pembicara;
        $deskripsi          = $request->deskripsi;
        $link               = $request->link;

        $newImageUrl = $request->old_image;

        if ($request->hasFile('gambar')) {

            // Hapus gambar lama dari storage Supabase
            if ($request->old_image) {
                $this->deleteFromStorage($request->old_image, 'seminar-images');
            }

            // Upload gambar baru
            $bucketName = "seminar-images";
            $file = $request->file('gambar');

            $fileName = "seminar_" . time() . "." . $file->getClientOriginalExtension();
            $fileContent = file_get_contents($file);

            $upload = Http::withHeaders([
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
            ])
            ->attach(
                'file',                       // field name
                $fileContent,                 // isi file
                $fileName                     // nama file
            )->post(
                $this->supabaseStorage . '/seminar-images/' . $fileName);

            if ($upload->failed()) {
                return dd($upload->json());
            }

            // URL baru
            $newImageUrl = $this->supabaseStorage . "/" . $bucketName . "/" . $fileName;
        }


        $query = <<<GRAPHQL
        mutation UpdateSeminar(
            \$id: ID!,
            \$nama: String!,
            \$tanggalPelaksanaan: Date!,
            \$mulaiPendaftaran: Date!,
            \$akhirPendaftaran: Date!,
            \$lokasi: String!,
            \$pembicara: String!,
            \$deskripsi: String!,
            \$link: String!,
            \$imageUrl: String!
        ) {
            updateseminarCollection(
                set: {
                    nama_seminar: \$nama,
                    tanggal_pelaksanaan: \$tanggalPelaksanaan,
                    mulai_pendaftaran: \$mulaiPendaftaran,
                    akhir_pendaftaran: \$akhirPendaftaran,
                    lokasi: \$lokasi,
                    pembicara: \$pembicara,
                    deskripsi: \$deskripsi,
                    link_pendaftaran: \$link
                    image_url: \$imageUrl
                },
                filter: { seminar_id: { eq: \$id } }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey'        => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type'  => 'application/json'
        ])->post($this->supabaseUrl, [
            'query' => $query,
            'variables' => [
                'id'                  => $id,
                'nama'                => $nama,
                'tanggalPelaksanaan'  => $tanggalPelaksanaan,
                'mulaiPendaftaran'    => $mulaiPendaftaran,
                'akhirPendaftaran'    => $akhirPendaftaran,
                'lokasi'              => $lokasi,
                'pembicara'           => $pembicara,
                'deskripsi'           => $deskripsi,
                'link'                => $link,
                'imageUrl'           => $newImageUrl,
            ]
        ]);

        if ($response->failed()) {
            return dd($response->json());
        }

        return redirect()->back()->with('success', 'Seminar berhasil diperbarui!');
    }

    private function deleteFromStorage($imageUrl, $bucket)
    {
        if (!$imageUrl) return;
        $bucketName = $bucket;

        dd("Deleting image from storage: $imageUrl");

        $fileName = basename($imageUrl);

        $url = $this->supabaseStorage . "/$bucketName/$fileName";

        $response = Http::withHeaders([
            'apikey' => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type' => 'application/json',
        ])->delete($url);
        dd($url, $response->json());

        return $response;
    }


    public function deleteSeminar(Request $request)
    {
        $id = $request->id;
        $imageUrl = $request->image_url;

        if ($imageUrl) {
            $this->deleteFromStorage($imageUrl, 'seminar-images');
        }
    
        $query = <<<GRAPHQL
        mutation DeleteSeminar(\$id: ID!) {
            deleteFromseminarCollection(
                filter: { seminar_id: { eq: \$id } }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey'        => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type'  => 'application/json'
        ])->post($this->supabaseUrl, [
            'query' => $query,
            'variables' => [ 'id' => $id ]
        ]);

        if ($response->failed()) {
            return dd($response->json());
        }

        return redirect()->back()->with('success', 'Seminar berhasil dihapus!');
    }

    // BEASISWA CRUD
    public function storeBeasiswa(Request $request)
    {
        // dd($request->all());

        // Validasi input
        $request->validate([
            'nama'                => 'required|string|max:255',
            'jenjang' => 'required|string|max:100',
            'mulai_pendaftaran'   => 'required|date',
            'akhir_pendaftaran'   => 'required|date',
            'syarat'              => 'required|string|max:255',
            'benefit'           => 'required|string|max:255',
            'pemberi'           => 'required|string',
            'link'                => 'required|string',
            'gambar'              => 'nullable|image|max:2048',
        ]);

        $imageUrl = null;

        //upload ke supabase storage jika ada gambar
        if ($request->hasFile('gambar')) {

            $bucketName = 'beasiswa_images';

            $file = $request->file('gambar');
            $fileName = 'beasiswa_' . time() . '.' . $file->getClientOriginalExtension();
            $fileContent = file_get_contents($file);

            // Upload ke Supabase Storage
            $upload = Http::withHeaders([
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
            ])
            ->attach(
                'file',                       // field name
                $fileContent,                 // isi file
                $fileName                     // nama file
            )
            ->post($this->supabaseStorage . '/beasiswa_images/' . $fileName);

            if ($upload->failed()) {
                return dd($upload->json());
            }

            // URL file
            $imageUrl = $this->supabaseStorage . '/' . $bucketName . '/' . $fileName;
        }

        // Ambil data request
        $nama                = $request->nama;
        $jenjangBeasiswa  = $request->jenjang;
        $mulaiPendaftaran    = $request->mulai_pendaftaran;
        $akhirPendaftaran    = $request->akhir_pendaftaran;
        $syarat              = $request->syarat;
        $benefit           = $request->benefit;
        $pemberi           = $request->pemberi;
        $link                = $request->link;

        // GraphQL Mutation
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

        // Kirim ke Supabase
        $response = Http::withHeaders([
            'apikey'        => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type'  => 'application/json'
        ])->post($this->supabaseUrl, [
            'query' => $query,
            'variables' => [
                'nama'                => $nama,
                'jenjangBeasiswa'  => $jenjangBeasiswa,
                'mulaiPendaftaran'    => $mulaiPendaftaran,
                'akhirPendaftaran'    => $akhirPendaftaran,
                'syarat'              => $syarat,
                'benefit'           => $benefit,
                'pemberi'           => $pemberi,
                'link'                => $link,
                'gambar'              => $imageUrl ?? '',
            ]
        ]);

        // Jika error
        if ($response->failed()) {
            return dd($response->json());
        }

        return back()->with('success', 'beasiswa berhasil ditambahkan!');
    }

    public function updateBeasiswa(Request $request)
    {
        // dd($request->all());
        // Validasi
        $request->validate([
            'id'                  => 'required|integer',
            'nama'                => 'required|string|max:255',
            'jenjang'             => 'required|string|max:100',
            'mulai_pendaftaran'   => 'required|date',
            'akhir_pendaftaran'   => 'required|date',
            'syarat'              => 'required|string|max:255',
            'benefit'             => 'required|string|max:255',
            'pemberi'             => 'required|string',
            'link'                => 'required|string',
            'gambar'              => 'nullable|image|max:2048', // <--- validasi gambar
            'old_image'           => 'nullable|string',         // <--- gambar lama
            
        ]);

        // Ambil variabel
        $id                 = $request->id;
        $nama               = $request->nama;
        $jenjang            = $request->jenjang;
        $mulaiPendaftaran   = $request->mulai_pendaftaran;
        $akhirPendaftaran   = $request->akhir_pendaftaran;
        $syarat             = $request->syarat;
        $benefit            = $request->benefit;
        $pemberi            = $request->pemberi;
        $link               = $request->link;

        $newImageUrl = $request->old_image;

        if ($request->hasFile('gambar')) {

            // Hapus gambar lama dari storage Supabase
            if ($request->old_image) {
                $this->deleteFromStorage($request->old_image, 'beasiswa_images');
            }

            // Upload gambar baru
            $bucketName = "beasiswa_images";
            $file = $request->file('gambar');

            $fileName = "beasiswa_" . time() . "." . $file->getClientOriginalExtension();
            $fileContent = file_get_contents($file);

            $upload = Http::withHeaders([
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
            ])
            ->attach(
                'file',                       // field name
                $fileContent,                 // isi file
                $fileName                     // nama file
            )->post(
                $this->supabaseStorage . '/beasiswa_images/' . $fileName);

            if ($upload->failed()) {
                return dd($upload->json());
            }

            // URL baru
            $newImageUrl = $this->supabaseStorage . "/" . $bucketName . "/" . $fileName;
        }

        $query = <<<GRAPHQL
        mutation UpdateBeasiswa(
            \$id: ID!,
            \$nama: String!,
            \$jenjang: String!,
            \$mulaiPendaftaran: Date!,
            \$akhirPendaftaran: Date!,
            \$syarat: String!,
            \$benefit: String!,
            \$pemberi: String!,
            \$link: String!,
            \$gambar: String!
        ) {
            updatebeasiswaCollection(
                set: {
                    nama_beasiswa: \$nama,
                    jenjang_beasiswa: \$jenjang,
                    mulai_pendaftaran: \$mulaiPendaftaran,
                    akhir_pendaftaran: \$akhirPendaftaran,
                    syarat_beasiswa: \$syarat,
                    benefit_beasiswa: \$benefit,
                    pemberi_beasiswa: \$pemberi,
                    link_pendaftaran: \$link,
                    image_url: \$gambar
                },
                filter: { beasiswa_id: { eq: \$id } }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey'        => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type'  => 'application/json'
        ])->post($this->supabaseUrl, [
            'query' => $query,
            'variables' => [
                'id'                => $id,
                'nama'              => $nama,
                'jenjang'           => $jenjang,
                'mulaiPendaftaran'  => $mulaiPendaftaran,
                'akhirPendaftaran'  => $akhirPendaftaran,
                'syarat'            => $syarat,
                'benefit'           => $benefit,
                'pemberi'           => $pemberi,
                'link'              => $link,
                'gambar'            => $newImageUrl,
            ]
        ]);

        if ($response->failed()) {
            return dd($response->json());
        }

        return redirect()->back()->with('success', 'Beasiswa berhasil diperbarui!');
    }

    public function deleteBeasiswa(Request $request)
    {
        $id = $request->id;
        $imageUrl = $request->image_url;

        if ($imageUrl) {
            $this->deleteFromStorage($imageUrl, 'beasiswa-images');
        }
    
        $query = <<<GRAPHQL
        mutation DeleteBeasiswa(\$id: ID!) {
            deleteFrombeasiswaCollection(
                filter: { beasiswa_id: { eq: \$id } }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey'        => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type'  => 'application/json'
        ])->post($this->supabaseUrl, [
            'query' => $query,
            'variables' => [ 'id' => $id ]
        ]);

        if ($response->failed()) {
            return dd($response->json());
        }

        return redirect()->back()->with('success', 'Beasiswa berhasil dihapus!');
    }

    // LOMBA CRUD
    public function storeLomba(Request $request)
    {
        // dd($request->all());

        // Validasi input
        $request->validate([
            'nama'                => 'required|string|max:255',
            'tanggal_pelaksanaan' => 'required|date',
            'mulai_pendaftaran'   => 'required|date',
            'akhir_pendaftaran'   => 'required|date',
            'lokasi'              => 'required|string|max:255',
            'kategori'            => 'required|string|max:255',
            'deskripsi'           => 'required|string',
            'penyelenggara'       => 'required|string',
            'link'                => 'required|string',
            'gambar'              => 'nullable|image|max:2048',
        ]);

        $imageUrl = null;

        //upload ke supabase storage jika ada gambar
        if ($request->hasFile('gambar')) {

            $bucketName = 'lomba_images';

            $file = $request->file('gambar');
            $fileName = 'lomba_' . time() . '.' . $file->getClientOriginalExtension();
            $fileContent = file_get_contents($file);

            // Upload ke Supabase Storage
            $upload = Http::withHeaders([
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
            ])
            ->attach(
                'file',                       // field name
                $fileContent,                 // isi file
                $fileName                     // nama file
            )
            ->post($this->supabaseStorage . '/lomba_images/' . $fileName);

            if ($upload->failed()) {
                return dd($upload->json());
            }

            // URL file
            $imageUrl = $this->supabaseStorage . '/' . $bucketName . '/' . $fileName;
        }

        // Ambil data request
        $nama                = $request->nama;
        $tanggalPelaksanaan = $request->tanggal_pelaksanaan;
        $mulaiPendaftaran    = $request->mulai_pendaftaran;
        $akhirPendaftaran    = $request->akhir_pendaftaran;
        $lokasi             = $request->lokasi;
        $kategori           = $request->kategori;
        $deskripsi           = $request->deskripsi;
        $penyelenggara           = $request->penyelenggara;
        $link                = $request->link;

        // GraphQL Mutation
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

        // Kirim ke Supabase
        $response = Http::withHeaders([
            'apikey'        => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type'  => 'application/json'
        ])->post($this->supabaseUrl, [
            'query' => $query,
            'variables' => [
                'nama'                => $nama,
                'tanggalPelaksanaan'  => $tanggalPelaksanaan,
                'mulaiPendaftaran'    => $mulaiPendaftaran,
                'akhirPendaftaran'    => $akhirPendaftaran,
                'lokasi'              => $lokasi,
                'kategori'            => $kategori,
                'deskripsi'           => $deskripsi,
                'penyelenggara'       => $penyelenggara,
                'link'                => $link,
                'gambar'              => $imageUrl ?? '',
            ]
        ]);

        // Jika error
        if ($response->failed()) {
            return dd($response->json());
        }

        return back()->with('success', 'Lomba berhasil ditambahkan!');
    }

    public function updateLomba(Request $request)
    {
        // dd($request->all());
        // Validasi
        $request->validate([
            'id'                  => 'required|integer',
            'nama'                => 'required|string|max:255',
            'tanggal_pelaksanaan' => 'required|date',
            'mulai_pendaftaran'   => 'required|date',
            'akhir_pendaftaran'   => 'required|date',
            'lokasi'              => 'required|string|max:255',
            'kategori'            => 'required|string|max:255',
            'deskripsi'           => 'required|string|max:255',
            'penyelenggara'       => 'required|string|max:255',
            'link'                => 'required|string',
            'gambar'              => 'nullable|image|max:2048', // <--- validasi gambar
            'old_image'           => 'nullable|string',         // <--- gambar lama
            
        ]);

        // Ambil variabel
        $id                 = $request->id;
        $nama               = $request->nama;
        $tanggalPelaksanaan= $request->tanggal_pelaksanaan;
        $mulaiPendaftaran   = $request->mulai_pendaftaran;
        $akhirPendaftaran   = $request->akhir_pendaftaran;
        $lokasi             = $request->lokasi;
        $kategori           = $request->kategori;
        $deskripsi          = $request->deskripsi;
        $penyelenggara      = $request->penyelenggara;
        $link               = $request->link;

        $newImageUrl = $request->old_image;

        if ($request->hasFile('gambar')) {

            // Hapus gambar lama dari storage Supabase
            if ($request->old_image) {
                $this->deleteFromStorage($request->old_image, 'lomba_images');
            }

            // Upload gambar baru
            $bucketName = "lomba_images";
            $file = $request->file('gambar');

            $fileName = "lomba_" . time() . "." . $file->getClientOriginalExtension();
            $fileContent = file_get_contents($file);

            $upload = Http::withHeaders([
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
            ])
            ->attach(
                'file',                       // field name
                $fileContent,                 // isi file
                $fileName                     // nama file
            )->post(
                $this->supabaseStorage . '/lomba_images/' . $fileName);

            if ($upload->failed()) {
                return dd($upload->json());
            }

            // URL baru
            $newImageUrl = $this->supabaseStorage . "/" . $bucketName . "/" . $fileName;
        }

        $query = <<<GRAPHQL
        mutation UpdateLomba(
            \$id: ID!,
            \$nama: String!,
            \$tanggalPelaksanaan: Date!,
            \$mulaiPendaftaran: Date!,
            \$akhirPendaftaran: Date!,
            \$lokasi: String!,
            \$kategori: String!,
            \$deskripsi: String!,
            \$penyelenggara: String!,
            \$link: String!,
            \$gambar: String!
        ) {
            updatelombaCollection(
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
                },
                filter: { lomba_id: { eq: \$id } }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey'        => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type'  => 'application/json'
        ])->post($this->supabaseUrl, [
            'query' => $query,
            'variables' => [
                'id'                => $id,
                'nama'              => $nama,
                'tanggalPelaksanaan'=> $tanggalPelaksanaan,
                'mulaiPendaftaran'  => $mulaiPendaftaran,
                'akhirPendaftaran'  => $akhirPendaftaran,
                'lokasi'            => $lokasi,
                'kategori'          => $kategori,
                'deskripsi'         => $deskripsi,
                'penyelenggara'     => $penyelenggara,
                'link'              => $link,
                'gambar'            => $newImageUrl,
            ]
        ]);

        if ($response->failed()) {
            return dd($response->json());
        }

        return redirect()->back()->with('success', 'Lomba berhasil diperbarui!');
    }

    public function deleteLomba(Request $request)
    {
        $id = $request->id;
        $imageUrl = $request->image_url;

        if ($imageUrl) {
            $this->deleteFromStorage($imageUrl, 'lomba-images');
        }
    
        $query = <<<GRAPHQL
        mutation DeleteLomba(\$id: ID!) {
            deleteFromlombaCollection(
                filter: { lomba_id: { eq: \$id } }
            ) {
                affectedCount
            }
        }
        GRAPHQL;

        $response = Http::withHeaders([
            'apikey'        => $this->supabaseKey,
            'Authorization' => 'Bearer ' . $this->supabaseKey,
            'Content-Type'  => 'application/json'
        ])->post($this->supabaseUrl, [
            'query' => $query,
            'variables' => [ 'id' => $id ]
        ]);

        if ($response->failed()) {
            return dd($response->json());
        }

        return redirect()->back()->with('success', 'Lomba berhasil dihapus!');
    }
}