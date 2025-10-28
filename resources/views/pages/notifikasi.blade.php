@extends('components._layouts.home')
@section('content')
    <div class="max-w-2xl space-y-6">
        <h1 class="text-white text-xl font-semibold mb-6">Notification</h1>

        {{-- Notifikasi List --}}
        <div class="space-y-4">
            {{-- Contoh notifikasi statis (bisa diganti data dinamis nanti) --}}
            @foreach (range(1,5) as $item)
                <div class="rounded-2xl bg-gradient-to-r from-[#122E32] to-[#0B1A1C] px-6 py-4 text-white shadow-md hover:shadow-lg transition">
                    <p class="font-medium">Your request to the Phoenix Team has been accepted</p>
                    <p class="text-sm text-gray-400 mt-1">September 15th</p>
                </div>
            @endforeach
        </div>
    </div>
@endsection
