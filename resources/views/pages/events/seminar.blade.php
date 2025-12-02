@extends('components._layouts.home')

@section('content')
@php
use Carbon\Carbon;
@endphp
<div class="max-w-7xl mx-auto grid grid-cols-12 gap-6">
    <div class="col-span-12 md:col-span-8 space-y-6">
        @include('components._ui.tabs', ['active' => 'seminar'])

        {{-- Loop through seminars provided by EventController (variable: $seminars) --}}
        @foreach ($seminars as $seminar)
        @include('components._ui.eventCard', [
        'title' => $seminar['nama_seminar'] ?? 'Unnamed Seminar',
        'image' => $seminar['image_url'] ?? null,
        'details' => [
        ['label' => 'Tanggal', 'value' => isset($seminar['tanggal_pelaksanaan']) && $seminar['tanggal_pelaksanaan'] ?
        Carbon::parse($seminar['tanggal_pelaksanaan'])->locale('id')->translatedFormat('j F Y') : '-'],
        ['label' => 'Daftar Mulai', 'value' => isset($seminar['mulai_pendaftaran']) && $seminar['mulai_pendaftaran'] ?
        Carbon::parse($seminar['mulai_pendaftaran'])->locale('id')->translatedFormat('j F Y') : '-'],
        ['label' => 'Daftar Akhir', 'value' => isset($seminar['akhir_pendaftaran']) && $seminar['akhir_pendaftaran'] ?
        Carbon::parse($seminar['akhir_pendaftaran'])->locale('id')->translatedFormat('j F Y') : '-'],
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