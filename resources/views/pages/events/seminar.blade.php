@extends('components._layouts.home')

@section('content')
    <div class="max-w-7xl mx-auto grid grid-cols-12 gap-6">
        <div class="col-span-12 md:col-span-8 space-y-6">
            @include('components._ui.tabs', ['active' => 'seminar'])

            {{-- Loop through seminars provided by EventController (variable: $seminars) --}}
            @foreach ($seminars as $seminar)
                @include('components._ui.eventCard', [
                    'title' => $seminar['nama_seminar'] ?? 'Unnamed Seminar',
                    'image' => Vite::asset('resources/images/Poster-seminar.jpg'),
                    'details' => [
                        ['label' => 'Tanggal', 'value' => $seminar['tanggal_pelaksanaan'] ?? '-'],
                        ['label' => 'Daftar Mulai', 'value' => $seminar['mulai_pendaftaran'] ?? '-'],
                        ['label' => 'Daftar Akhir', 'value' => $seminar['akhir_pendaftaran'] ?? '-'],
                        ['label' => 'Lokasi', 'value' => $seminar['lokasi'] ?? '-'],
                        ['label' => 'Pembicara', 'value' => $seminar['pembicara'] ?? '-'],
                        ['label' => 'Deskripsi', 'value' => $seminar['deskripsi'] ?? '-'],
                        ['label' => 'Link Daftar', 'value' => $seminar['link_pendaftaran'] ?? '-'],
                    ],
                    'button' => ['text' => 'Register', 'href' => $seminar['link_pendaftaran'] ?? '#'],
                ])
            @endforeach
        </div>

        <div class="col-span-12 md:col-span-4 space-y-6">
            @include('components._ui.searchBar')
            @include('components._ui.teamRecommendation')
        </div>
    </div>
@endsection

