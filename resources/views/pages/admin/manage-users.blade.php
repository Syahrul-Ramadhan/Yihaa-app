@extends('components._layouts.admin')
@section('content')
@php
    use Carbon\Carbon;
@endphp
<div class="container mx-auto px-4 py-6 bg-gradient-to-l from-[#163F44] to-[#020C0D] min-h-screen">
    <h1 class="text-3xl font-bold mb-6 text-[#ffffff]">Manage Users</h1>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-500/20 border border-green-500 rounded-lg text-green-400">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-500/20 border border-red-500 rounded-lg text-red-400">
            {{ session('error') }}
        </div>
    @endif

    <!-- All Materials Tab -->
    <div id="content-all" class="tab-content ">
        <!-- <h2 class="text-xl font-semibold mb-4 text-[#1CC8EE]">All Materials</h2> -->
        <div class="rounded-xl p-6" style="background: linear-gradient(to right, #122E32, #0B1A1C); box-shadow: 0 4px 10px rgba(0, 224, 255, 0.15);">
            <table class="min-w-full text-sm text-white">
                <thead>
                    <tr class="border-b border-[#1CC8EE]">
                        <th class="py-2 px-4 text-left">id</th>
                        <th class="py-2 px-4 text-left">username</th>
                        <th class="py-2 px-4 text-left">email</th>
                        <th class="py-2 px-4 text-left">role</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr class="border-b border-gray-700">
                        <td class="py-2 px-4">{{ $user['id'] ?? '-' }}</td>
                        <td class="py-2 px-4">{{ $user['name'] }}</td>
                        <td class="py-2 px-4">{{ $user['email'] }}</td>
                        <td class="py-2 px-4">{{ $user['role'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
