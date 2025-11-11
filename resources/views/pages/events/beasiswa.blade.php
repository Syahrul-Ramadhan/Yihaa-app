@extends('components._layouts.home')

@section('content')
    <div class="max-w-7xl mx-auto grid grid-cols-12 gap-6">
        <div class="col-span-12 md:col-span-8 space-y-6">
            @include('components._ui.tabs', ['active' => 'beasiswa'])

            {{-- Loop through beasiswas provided by EventController (variable: $beasiswas) --}}
            @foreach ($beasiswas ?? [] as $beasiswa)
                @include('components._ui.eventCard', [
                    'title' => $beasiswa['nama_beasiswa'] ?? 'Beasiswa',
                    'image' => Vite::asset('resources/images/Poster-Beasiswa.jpg'),
                    'details' => [
                        ['label' => 'Jenjang', 'value' => $beasiswa['jenjang_beasiswa'] ?? '-'],
                        ['label' => 'Mulai', 'value' => $beasiswa['mulai_pendaftaran'] ?? '-'],
                        ['label' => 'Akhir', 'value' => $beasiswa['akhir_pendaftaran'] ?? '-'],
                        ['label' => 'Syarat', 'value' => $beasiswa['syarat_beasiswa'] ?? '-'],
                        ['label' => 'Benefit', 'value' => $beasiswa['benefit_beasiswa'] ?? '-'],
                        ['label' => 'Pemberi', 'value' => $beasiswa['pemberi_beasiswa'] ?? '-'],
                        ['label' => 'Link Daftar', 'value' => $beasiswa['link_pendaftaran'] ?? '-'],
                    ],
                    'button' => ['text' => 'Apply', 'href' => $beasiswa['link_pendaftaran'] ?? '#'],
                ])
            @endforeach
        </div>
        <div class="col-span-12 md:col-span-4 space-y-6">
            @include('components._ui.searchBar')
            @include('components._ui.teamRecommendation')
        </div>
    </div>
@endsection

