@extends('components._layouts.home')
@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back Button -->
    <a href="{{ route('teams.index') }}" class="inline-flex items-center gap-2 text-white mb-6 hover:text-[#2aa3ef] transition">
        <i class="hgi hgi-stroke hgi-arrow-left-01 text-xl"></i>
        Back to Teams
    </a>

    <!-- Team Header Card -->
    <div class="bg-gradient-to-br from-[#163F44] to-[#020C0D] rounded-2xl p-8 mb-6 border border-[#2aa3ef20]">
        <div class="flex flex-col md:flex-row gap-6 items-start md:items-center">
            <!-- Team Logo -->
            <div class="w-32 h-32 bg-[#2aa3ef20] rounded-2xl flex items-center justify-center overflow-hidden">
                @if($team['team_logo'])
                    <img src="{{ $team['team_logo'] }}" alt="{{ $team['team_name'] }}" class="w-full h-full object-cover">
                @else
                    <i class="hgi hgi-stroke hgi-group text-6xl text-[#2aa3ef]"></i>
                @endif
            </div>

            <!-- Team Info -->
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-3xl font-bold text-white">{{ $team['team_name'] }}</h1>
                    <span class="px-3 py-1 bg-[#2aa3ef20] text-[#2aa3ef] rounded-full text-sm">
                        {{ ucfirst($team['team_status']) }}
                    </span>
                </div>
                <p class="text-gray-400 mb-4">{{ $team['member_count'] }}/{{ $team['member_limit'] }} Members</p>
                
                <!-- Action Buttons -->
                <div class="flex gap-3">
                    @if($isMember)
                        <a href="{{ route('chat.show', $team['team_id']) }}" 
                            class="px-6 py-3 bg-[#2aa3ef] hover:bg-[#2aa3efcc] text-white font-semibold rounded-xl transition">
                            <i class="hgi hgi-stroke hgi-message-02 mr-2"></i>
                            Open Chat
                        </a>
                    @elseif($isPending)
                        <button disabled class="px-6 py-3 bg-gray-600 text-gray-300 font-semibold rounded-xl cursor-not-allowed">
                            <i class="hgi hgi-stroke hgi-time-02 mr-2"></i>
                            Request Pending
                        </button>
                    @else
                        <form action="{{ route('teams.join', $team['team_id']) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-6 py-3 bg-[#2aa3ef] hover:bg-[#2aa3efcc] text-white font-semibold rounded-xl transition">
                                <i class="hgi hgi-stroke hgi-user-add-01 mr-2"></i>
                                Join Team
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Team Description -->
    <div class="bg-[#ffffff0a] rounded-2xl p-6 mb-6 border border-[#2aa3ef20]">
        <h2 class="text-xl font-bold text-white mb-4">About This Team</h2>
        <p class="text-gray-300 leading-relaxed">
            {{ $team['team_desc'] ?? 'No description provided.' }}
        </p>
    </div>

    <!-- Team Members -->
    <div class="bg-[#ffffff0a] rounded-2xl p-6 border border-[#2aa3ef20]">
        <h2 class="text-xl font-bold text-white mb-4">Team Members ({{ count($members) }})</h2>
        <div class="space-y-3">
            @forelse($members as $member)
                <div class="flex items-center justify-between p-3 bg-[#ffffff05] rounded-lg">
                    <div class="flex items-center gap-3">
                        <img src="{{ $member['avatar_url'] ?? 'https://qdfotopajdiuailyeprh.supabase.co/storage/v1/object/public/avatars/default-user.jpg' }}" 
                            alt="{{ $member['name'] }}" 
                            class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <p class="text-white font-semibold">{{ $member['name'] }}</p>
                            <p class="text-sm text-gray-400">{{ ucfirst($member['role']) }}</p>
                        </div>
                    </div>
                    @if($member['role'] === 'leader')
                        <span class="px-3 py-1 bg-yellow-500/20 text-yellow-500 rounded-full text-xs">Leader</span>
                    @endif
                </div>
            @empty
                <p class="text-gray-400 text-center py-4">No members yet</p>
            @endforelse
        </div>
    </div>
</div>

@if(session('success'))
    <script>
        alert('{{ session('success') }}');
    </script>
@endif
@endsection
