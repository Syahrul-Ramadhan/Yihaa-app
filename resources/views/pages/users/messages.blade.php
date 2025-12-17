@extends('components._layouts.messages')

@section('content')
<div class="flex h-screen bg-[#0E1419] text-white">

    {{-- SIDEBAR TIM --}}
    <div x-data="{ 
        showRef: false,
        keyword: '',
        teams: @js($teams),
        activeTeamId: {{ isset($activeTeam) ? (int)$activeTeam['team_id'] : 'null' }}
    }" class="w-80 border-r border-gray-700 flex flex-col">

        {{-- Header Sidebar --}}
        <div class="flex items-center gap-3 p-4 border-b border-gray-700">
            <button onclick="window.location.href='/home'" class="text-gray-400 hover:text-white">
                <i class="hgi hgi-stroke hgi-arrow-left-02"></i>
            </button>

            <input type="text" placeholder="Search team..." x-model="keyword"
                class="bg-[#1A232B] text-sm px-3 py-2 rounded-md w-full focus:outline-none" />

            {{-- Reset Search --}}
            <button x-show="keyword.length" @click="keyword = ''" class="text-gray-400 hover:text-white">
                <i class="hgi hgi-stroke hgi-cancel-01 text-lg"></i>
            </button>
        </div>

        {{-- Daftar Tim --}}
        <div class="flex-1 overflow-y-auto space-y-1 px-1">
            <template x-for="team in teams.filter(t => 
                t.team_name.toLowerCase().includes(keyword.toLowerCase())
            )" :key="team.team_id">
                <a :href="`/chat/${team.team_id}`"
                    class="flex items-center gap-3 px-3 py-2 rounded hover:bg-neutral-900"
                    :class="{ 'bg-neutral-800': team.team_id === activeTeamId }">
                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden">
                        <template x-if="team.team_logo">
                            <img :src="team.team_logo" alt="team-logo" class="w-full h-full rounded-full object-cover">
                        </template>
                        <template x-if="!team.team_logo">
                            <i class="hgi hgi-stroke hgi-group text-sm text-[#2aa3ef]"></i>
                        </template>
                    </div>

                    <span class="text-sm truncate" x-text="team.team_name"></span>
                </a>
            </template>

            {{-- Jika hasil kosong --}}
            <div x-show="teams.filter(t => 
                t.team_name.toLowerCase().includes(keyword.toLowerCase())
            ).length === 0" class="text-center text-gray-400 text-sm mt-4">
                Team tidak ditemukan
            </div>
        </div>
    </div>

    {{-- AREA CHAT --}}
    <div class="flex-1 flex flex-col bg-[#0E1419]">

        {{-- Jika belum pilih tim --}}
        @if (!isset($activeTeam))
        <div class="flex flex-col items-center justify-center h-full">
            <p class="text-gray-400 text-lg">Select a chat to start messaging</p>
        </div>

        {{-- Jika sudah pilih tim --}}
        @else
        {{-- Header Chat --}}
        <div class="border-b border-gray-700 p-4 font-semibold flex items-center gap-3">
            @if(isset($activeTeam['team_logo']) && $activeTeam['team_logo'])
            <img src="{{ $activeTeam['team_logo'] }}" alt="team-logo" class="rounded-full object-cover" width="32"
                height="32">
            @else
            <div class="w-8 h-8 bg-[#2aa3ef20] rounded-full flex items-center justify-center">
                <i class="hgi hgi-stroke hgi-group text-lg text-[#2aa3ef]"></i>
            </div>
            @endif
            <span>{{ $activeTeam['team_name'] ?? 'Team Chat' }}</span>
        </div>

        {{-- Daftar Pesan --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
            @if (isset($messages) && count($messages) > 0)
            @foreach ($messages as $msg)
            <div class="flex gap-3 items-start">
                <img src="{{ $msg['users']['avatar_url'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($msg['users']['name'] ?? 'User') }}"
                    alt="avatar" class="w-8 h-8 rounded-full object-cover object-center">
                <div>
                    <p class="text-sm font-semibold">{{ $msg['users']['name'] ?? 'Unknown' }}</p>
                    <p class="text-sm text-gray-300">{{ $msg['text_chat'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ \Carbon\Carbon::parse($msg['created_at'])->diffForHumans() }}</p>
                </div>
            </div>
            @endforeach
            @else
            <p class="text-gray-400 text-center">Belum ada pesan di {{ $activeTeam['team_name'] ?? 'team ini' }}...</p>
            @endif
        </div>

        {{-- Input Chat --}}
        <div class="p-4 border-t border-gray-700">
            <form action="{{ route('chat.send', $activeTeam['team_id']) }}" method="POST" class="flex gap-3">
                @csrf
                <input type="text" name="message" placeholder="Write a message..." required
                    class="flex-1 bg-[#1A232B] px-4 py-2 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-[#2aa3ef]">
                <button type="submit"
                    class="bg-[#2aa3ef] px-6 py-2 rounded-lg hover:bg-[#2aa3efcc] text-white font-semibold transition flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                    </svg>
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection