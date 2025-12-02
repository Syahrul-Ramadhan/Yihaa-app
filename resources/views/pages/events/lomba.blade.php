@extends('components._layouts.home')

@section('content')
@php
use Carbon\Carbon;
@endphp
<div class="max-w-7xl mx-auto grid grid-cols-12 gap-6">
    <div class="col-span-12 md:col-span-8 space-y-6">
        @include('components._ui.tabs', ['active' => 'lomba'])

        {{-- Loop through lomba items provided by EventController (variable: $lomba) --}}
        @foreach ($lombas as $lomba)
        @include('components._ui.eventCard', [
        'title' => $lomba['nama_lomba'] ?? 'Lomba',
        'image' => $lomba['image_url'],
        'details' => [
        ['label' => 'Tanggal', 'value' => isset($lomba['tanggal_pelaksanaan']) && $lomba['tanggal_pelaksanaan'] ?
        Carbon::parse($lomba['tanggal_pelaksanaan'])->locale('id')->translatedFormat('j F Y') : '-'],
        ['label' => 'Mulai', 'value' => isset($lomba['mulai_pendaftaran']) && $lomba['mulai_pendaftaran'] ?
        Carbon::parse($lomba['mulai_pendaftaran'])->locale('id')->translatedFormat('j F Y') : '-'],
        ['label' => 'Akhir', 'value' => isset($lomba['akhir_pendaftaran']) && $lomba['akhir_pendaftaran'] ?
        Carbon::parse($lomba['akhir_pendaftaran'])->locale('id')->translatedFormat('j F Y') : '-'],
        ['label' => 'Lokasi', 'value' => $lomba['lokasi'] ?? '-'],
        ['label' => 'Kategori', 'value' => $lomba['kategori_lomba'] ?? '-'],
        ['label' => 'Deskripsi', 'value' => $lomba['deskripsi'] ?? '-'],
        ['label' => 'Penyelenggara', 'value' => $lomba['penyelenggara'] ?? '-'],
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