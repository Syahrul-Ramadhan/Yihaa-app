@extends('components._layouts.home')

@section('content')
    <div class="max-w-7xl mx-auto grid grid-cols-12 gap-6">
        <div class="col-span-12 md:col-span-8 space-y-6">
            @include('components._ui.tabs', ['active' => 'lomba'])

            {{-- Loop through lomba items provided by EventController (variable: $lomba) --}}
            @foreach ($lombas as $lomba)
                @include('components._ui.eventCard', [
                    'title' => $lomba['nama_lomba'] ?? 'Lomba',
                    'image' => Vite::asset('resources/images/Poster-Lomba.jpeg'),
                    'details' => [
                        ['label' => 'Tanggal', 'value' => $lomba['tanggal_pelaksanaan'] ?? '-'],
                        ['label' => 'Mulai', 'value' => $lomba['mulai_pendaftaran'] ?? '-'],
                        ['label' => 'Akhir', 'value' => $lomba['akhir_pendaftaran'] ?? '-'],
                        ['label' => 'Lokasi', 'value' => $lomba['lokasi'] ?? '-'],
                        ['label' => 'Jenis', 'value' => $lomba['jenis_lomba'] ?? '-'],
                        ['label' => 'Jenjang', 'value' => $lomba['jenjang_lomba'] ?? '-'],
                        ['label' => 'Deskripsi', 'value' => $lomba['deskripsi_lomba'] ?? '-'],
                        ['label' => 'Link Daftar', 'value' => $lomba['link_pendaftaran'] ?? '-'],
                    ],
                    'button' => ['text' => 'Register', 'href' => $lomba['link_pendaftaran'] ?? '#'],
                ])
            @endforeach
        </div>
        <div class="col-span-12 md:col-span-4 space-y-6">
            @include('components._ui.searchBar')
            @include('components._ui.teamRecommendation')
        </div>
    </div>
@endsection

