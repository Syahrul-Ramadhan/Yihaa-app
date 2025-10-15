@php
    $items = [1,2,3];
@endphp

@extends('components._layouts.home')

@section('content')
    <div class="max-w-7xl mx-auto grid grid-cols-12 gap-6">
        <div class="col-span-12 md:col-span-8 space-y-6">
            @include('components._ui.tabs', ['active' => 'lomba'])

            @foreach ($items as $i)
                @include('components._ui.eventCard', [
                    'title' => 'Badminton Competition',
                    'image' => Vite::asset('resources/images/Poster-Lomba.jpeg'),
                ])
            @endforeach
        </div>
        <div class="col-span-12 md:col-span-4 space-y-6">
            @include('components._ui.searchBar')
            @include('components._ui.teamRecommendation')
        </div>
    </div>
@endsection

