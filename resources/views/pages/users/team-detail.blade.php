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
                    @if($team['team_status'] === 'closed')
                        <span class="px-3 py-1 bg-red-500/20 text-red-400 rounded-full text-sm">
                            Closed
                        </span>
                    @else
                        <span class="px-3 py-1 bg-[#2aa3ef20] text-[#2aa3ef] rounded-full text-sm">
                            Open
                        </span>
                    @endif
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
                        
                        @if($team['leader_id'] == session('user_id'))
                            <button 
                                onclick="showEditTeamModal()"
                                class="px-6 py-3 bg-[#2aa3ef] hover:bg-[#2aa3efcc] text-white font-semibold rounded-xl transition">
                                <i class="hgi hgi-stroke hgi-pencil-edit-02 mr-2"></i>
                                Edit Team
                            </button>
                            <button 
                                onclick="document.getElementById('deleteTeamModal').classList.remove('hidden')"
                                class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition">
                                <i class="hgi hgi-stroke hgi-delete-02 mr-2"></i>
                                Delete Team
                            </button>
                        @endif
                    @elseif($isPending)
                        <button disabled class="px-6 py-3 bg-gray-600 text-gray-300 font-semibold rounded-xl cursor-not-allowed">
                            <i class="hgi hgi-stroke hgi-time-02 mr-2"></i>
                            Request Pending
                        </button>
                    @else
                        @if($team['member_count'] >= $team['member_limit'])
                            <button 
                                onclick="showTeamFullModal()"
                                class="px-6 py-3 bg-gray-600 text-gray-300 font-semibold rounded-xl">
                                <i class="hgi hgi-stroke hgi-lock-02 mr-2"></i>
                                Team Full
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
    <div class="bg-[#ffffff0a] rounded-2xl p-6 border border-[#2aa3ef20] mb-6">
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
                    <div class="flex items-center gap-2">
                        @if($member['role'] === 'leader')
                            <span class="px-3 py-1 bg-yellow-500/20 text-yellow-500 rounded-full text-xs">Leader</span>
                        @elseif($team['leader_id'] == session('user_id') && $member['user_id'] != session('user_id'))
                            <button 
                                onclick="showKickModal('{{ $member['user_id'] }}', '{{ $member['name'] }}')"
                                class="px-3 py-1 bg-red-500/20 hover:bg-red-500/40 text-red-400 rounded-lg text-xs transition" 
                                title="Kick member">
                                <i class="hgi hgi-stroke hgi-user-remove-02"></i>
                                Kick
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Pending Invites (Only for Leader) -->
    @if($team['leader_id'] == session('user_id') && count($pendingMembers) > 0)
    <div class="bg-[#ffffff0a] rounded-2xl p-6 border border-yellow-500/20 mb-6">
        <h2 class="text-xl font-bold text-white mb-4">
            <i class="hgi hgi-stroke hgi-time-02 text-yellow-500 mr-2"></i>
            Pending Join Requests ({{ count($pendingMembers) }})
        </h2>
        <div class="space-y-3">
            @foreach($pendingMembers as $pending)
                <div class="flex items-center justify-between p-3 bg-[#ffffff05] rounded-lg">
                    <div class="flex items-center gap-3">
                        <img src="{{ $pending['avatar_url'] ?? 'https://qdfotopajdiuailyeprh.supabase.co/storage/v1/object/public/avatars/default-user.jpg' }}" 
                            alt="{{ $pending['name'] }}" 
                            class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <p class="text-white font-semibold">{{ $pending['name'] }}</p>
                            <p class="text-sm text-yellow-400">Waiting for approval</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <form action="{{ route('teams.acceptMember', [$team['team_id'], $pending['user_id']]) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-semibold rounded-lg transition">
                                <i class="hgi hgi-stroke hgi-checkmark-circle-02 mr-1"></i>
                                Accept
                            </button>
                        </form>
                        <form action="{{ route('teams.rejectMember', [$team['team_id'], $pending['user_id']]) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-lg transition">
                                <i class="hgi hgi-stroke hgi-cancel-01 mr-1"></i>
                                Decline
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Success/Error Modal -->
@if(session('success') || session('error'))
<div id="alertModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-[#0D1517] rounded-2xl p-6 w-full max-w-md border {{ session('success') ? 'border-green-500/30' : 'border-red-500/30' }} transform scale-100 animate-[slideUp_0.3s_ease-out]">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 {{ session('success') ? 'bg-green-500/20' : 'bg-red-500/20' }} rounded-full flex items-center justify-center">
                <i class="hgi hgi-stroke {{ session('success') ? 'hgi-checkmark-circle-02 text-green-500' : 'hgi-alert-02 text-red-500' }} text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-white">{{ session('success') ? 'Success!' : 'Error!' }}</h3>
        </div>
        
        <p class="text-gray-300 mb-6">
            {{ session('success') ?? session('error') }}
        </p>
        
        <button 
            onclick="document.getElementById('alertModal').remove(); window.location.reload();"
            class="w-full px-4 py-3 {{ session('success') ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }} text-white font-semibold rounded-lg transition">
            OK
        </button>
    </div>
</div>

<style>
@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endif

<!-- Delete Team Modal -->
<div id="deleteTeamModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-[#0D1517] rounded-2xl p-6 w-full max-w-md border border-red-500/30">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-red-500/20 rounded-full flex items-center justify-center">
                <i class="hgi hgi-stroke hgi-alert-02 text-2xl text-red-500"></i>
            </div>
            <h3 class="text-xl font-bold text-white">Delete Team</h3>
        </div>
        
        <p class="text-gray-300 mb-6">
            Are you sure you want to delete this team? This action cannot be undone and will remove all team members.
        </p>
        
        <div class="flex gap-3">
            <button 
                onclick="document.getElementById('deleteTeamModal').classList.add('hidden')"
                class="flex-1 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition">
                Cancel
            </button>
            <form action="{{ route('teams.destroy', $team['team_id']) }}" method="POST" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition">
                    Delete
                </button>
            </form>
        </div>
    </div>
    </div>
</div>

<!-- Kick Member Modal -->
<div id="kickMemberModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-[#0D1517] rounded-2xl p-6 w-full max-w-md border border-red-500/30 transform scale-100 animate-[slideUp_0.3s_ease-out]">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-red-500/20 rounded-full flex items-center justify-center">
                    <i class="hgi hgi-stroke hgi-user-remove-02 text-2xl text-red-500"></i>
                </div>
                <h3 class="text-xl font-bold text-white">Kick Member</h3>
            </div>
            
            <p class="text-gray-300 mb-6">
                Are you sure you want to kick <span id="kickMemberName" class="font-semibold text-white"></span> from the team?
            </p>
            
            <div class="flex gap-3">
                <button 
                    onclick="closeKickModal()"
                    class="flex-1 px-4 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition">
                    Cancel
                </button>
                <form id="kickMemberForm" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition">
                        Kick Member
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Team Full Modal -->
<div id="teamFullModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-[#0D1517] rounded-2xl p-6 w-full max-w-md border border-yellow-500/30 transform scale-100 animate-[slideUp_0.3s_ease-out]">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-yellow-500/20 rounded-full flex items-center justify-center">
                    <i class="hgi hgi-stroke hgi-lock-02 text-2xl text-yellow-500"></i>
                </div>
                <h3 class="text-xl font-bold text-white">Team Full</h3>
            </div>
            
            <p class="text-gray-300 mb-6">
                This team has reached its maximum member capacity. You cannot send a join request at this time. Please try again when a spot becomes available.
            </p>
            
            <button 
                onclick="closeTeamFullModal()"
                class="w-full px-4 py-3 bg-[#2aa3ef] hover:bg-[#2aa3efcc] text-white font-semibold rounded-lg transition">
                Understood
            </button>
        </div>
    </div>
</div>

<!-- Edit Team Modal -->
<div id="editTeamModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-[#0D1517] rounded-2xl p-6 w-full max-w-2xl border border-[#2aa3ef30] transform scale-100 animate-[slideUp_0.3s_ease-out]">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 bg-[#2aa3ef]/20 rounded-full flex items-center justify-center">
                    <i class="hgi hgi-stroke hgi-pencil-edit-02 text-2xl text-[#2aa3ef]"></i>
                </div>
                <h3 class="text-xl font-bold text-white">Edit Team</h3>
            </div>
            
            <form action="{{ route('teams.update', $team['team_id']) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Team Name (Read Only) -->
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 font-semibold">Team Name</label>
                    <input type="text" value="{{ $team['team_name'] }}" disabled 
                        class="w-full px-4 py-3 bg-gray-700 text-gray-400 rounded-lg cursor-not-allowed">
                    <p class="text-xs text-gray-500 mt-1">Team name cannot be changed</p>
                </div>
                
                <!-- Team Description -->
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 font-semibold">Team Description</label>
                    <textarea name="team_desc" rows="4" 
                        class="w-full px-4 py-3 bg-[#1a1f23] border border-gray-700 text-white rounded-lg focus:border-[#2aa3ef] focus:outline-none"
                        placeholder="Enter team description...">{{ $team['team_desc'] }}</textarea>
                </div>
                
                <!-- Team Logo URL -->
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 font-semibold">Team Logo URL</label>
                    <input type="url" name="team_logo" value="{{ $team['team_logo'] }}" 
                        class="w-full px-4 py-3 bg-[#1a1f23] border border-gray-700 text-white rounded-lg focus:border-[#2aa3ef] focus:outline-none"
                        placeholder="https://example.com/logo.png">
                    <p class="text-xs text-gray-500 mt-1">Enter a valid image URL for team logo</p>
                </div>
                
                <!-- Member Limit -->
                <div class="mb-6">
                    <label class="block text-gray-300 mb-2 font-semibold">Member Limit</label>
                    <input type="number" name="member_limit" value="{{ $team['member_limit'] }}" 
                        min="{{ $team['member_count'] }}" max="20"
                        class="w-full px-4 py-3 bg-[#1a1f23] border border-gray-700 text-white rounded-lg focus:border-[#2aa3ef] focus:outline-none">
                    <p class="text-xs text-gray-500 mt-1">Current members: {{ $team['member_count'] }}. Cannot set limit below current member count.</p>
                </div>
                
                <div class="flex gap-3">
                    <button 
                        type="button"
                        onclick="closeEditTeamModal()"
                        class="flex-1 px-4 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-3 bg-[#2aa3ef] hover:bg-[#2aa3efcc] text-white font-semibold rounded-lg transition">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showKickModal(userId, userName) {
    const modal = document.getElementById('kickMemberModal');
    const form = document.getElementById('kickMemberForm');
    const nameSpan = document.getElementById('kickMemberName');
    
    // Set form action
    form.action = `/teams/{{ $team['team_id'] }}/kick/${userId}`;
    
    // Set member name
    nameSpan.textContent = userName;
    
    // Show modal
    modal.classList.remove('hidden');
}

function closeKickModal() {
    document.getElementById('kickMemberModal').classList.add('hidden');
}

function showTeamFullModal() {
    document.getElementById('teamFullModal').classList.remove('hidden');
}

function closeTeamFullModal() {
    document.getElementById('teamFullModal').classList.add('hidden');
}

function showEditTeamModal() {
    document.getElementById('editTeamModal').classList.remove('hidden');
}

function closeEditTeamModal() {
    document.getElementById('editTeamModal').classList.add('hidden');
}
</script>
@endsection
