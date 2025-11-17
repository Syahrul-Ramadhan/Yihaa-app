@extends('components._layouts.home')
@section('content')
    <div>
        <!-- Search Bar -->
        <form action="{{ route('teams.index') }}" method="GET" class="relative">
            <input 
                type="text" 
                name="search"
                value="{{ $search ?? '' }}"
                class="peer py-2.5 sm:py-3 px-4 ps-11 block w-full bg-gradient-to-l from-[#163F44] to-[#020C0D] border border-gray-600 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none text-white" 
                placeholder="Search team by name...">
            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-4 peer-disabled:opacity-50 peer-disabled:pointer-events-none">
                <i class="hgi hgi-stroke hgi-search-01 text-gray-400"></i>
            </div>
            @if(!empty($search))
                <a href="{{ route('teams.index') }}" class="absolute inset-y-0 end-0 flex items-center pe-4 text-gray-400 hover:text-white transition">
                    <i class="hgi hgi-stroke hgi-cancel-01"></i>
                </a>
            @endif
        </form>

        <section class="py-5">
            <!-- My Teams Section -->
            @if(!empty($myTeams))
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    <i class="hgi hgi-stroke hgi-star text-[#2aa3ef]"></i>
                    My Teams
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($myTeams as $team)
                        <a href="{{ route('teams.show', $team['team_id']) }}" class="bg-gradient-to-br from-[#2aa3ef20] to-[#ffffff0a] backdrop-blur-md border border-[#2aa3ef] p-5 rounded-2xl shadow-md text-white hover:scale-[1.02] hover:shadow-[#2aa3ef40] transition duration-300 cursor-pointer">
                            <!-- Header -->
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="bg-[#2aa3ef40] p-3 rounded-full w-14 h-14 flex items-center justify-center overflow-hidden">
                                        @if(isset($team['team_logo']) && $team['team_logo'])
                                            <img src="{{ $team['team_logo'] }}" alt="team logo" class="w-full h-full object-cover rounded-full">
                                        @else
                                            <i class="hgi hgi-stroke hgi-group text-2xl text-[#2aa3ef]"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-lg">{{ $team['team_name'] }}</h3>
                                        <p class="text-sm text-gray-400">
                                            {{ $team['member_count'] }}/{{ $team['member_limit'] }}
                                        </p>
                                    </div>
                                </div>
                                @if(isset($team['user_role']) && $team['user_role'] === 'leader')
                                    <span class="px-2 py-1 bg-yellow-500/20 text-yellow-400 text-xs rounded-full border border-yellow-500/40">Leader</span>
                                @endif
                            </div>

                            <!-- Description -->
                            <p class="text-sm text-gray-300 mb-5 line-clamp-2">{{ $team['team_desc'] }}</p>

                            <!-- View Details Button -->
                            <div class="w-full py-2 bg-[#2aa3ef] hover:bg-[#2aa3efcc] text-white font-semibold rounded-xl transition cursor-pointer text-center">
                                View Details
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-white">Cari Tim yang Cocok untukmu!</h2>
                <button 
                    onclick="document.getElementById('createTeamModal').classList.remove('hidden'); document.getElementById('createTeamModal').classList.add('flex');"
                    class="px-4 py-2 bg-[#2aa3ef] hover:bg-[#2aa3efcc] text-white font-semibold rounded-xl transition cursor-pointer flex items-center gap-2"
                >
                    <i class="hgi hgi-stroke hgi-add-circle"></i>
                    Create Team
                </button>
            </div>

            <!-- team container -->
            @if (empty($teams))
                <p class="text-gray-400 text-center">Tidak ada tim yang ditemukan.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($teams as $team)
                        <a href="{{ route('teams.show', $team['team_id']) }}" class="bg-[#ffffff0a] backdrop-blur-md border border-[#2aa3ef20] p-5 rounded-2xl shadow-md text-white hover:scale-[1.02] hover:border-[#2aa3ef80] transition duration-300 cursor-pointer">
                            <!-- Header -->
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="bg-[#2aa3ef20] p-3 rounded-full w-14 h-14 flex items-center justify-center overflow-hidden">
                                        @if(isset($team['team_logo']) && $team['team_logo'])
                                            <img src="{{ $team['team_logo'] }}" alt="team logo" class="w-full h-full object-cover rounded-full">
                                        @else
                                            <i class="hgi hgi-stroke hgi-group text-2xl text-[#2aa3ef]"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-lg">{{ $team['team_name'] }}</h3>
                                        <p class="text-sm text-gray-400">
                                            {{ $team['member_count'] }}/{{ $team['member_limit'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <p class="text-sm text-gray-300 mb-5 line-clamp-2">{{ $team['team_desc'] }}</p>

                            <!-- View Details Button -->
                            <div class="w-full py-2 bg-[#2aa3ef] hover:bg-[#2aa3efcc] text-white font-semibold rounded-xl transition cursor-pointer text-center">
                                View Details
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    <!-- Modal Create Team -->
    <div id="createTeamModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center">
        <div class="bg-[#0D1517] rounded-2xl p-6 w-full max-w-md mx-4 border border-[#2aa3ef20]">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-white">Create New Team</h3>
                <button onclick="document.getElementById('createTeamModal').classList.add('hidden'); document.getElementById('createTeamModal').classList.remove('flex');" class="text-gray-400 hover:text-white">
                    <i class="hgi hgi-stroke hgi-cancel-01 text-2xl"></i>
                </button>
            </div>
            
            <!-- Modal Form -->
            <form action="{{ route('teams.store') }}" method="POST" enctype="multipart/form-data" class="bg-[#0e1525] rounded-2xl p-8 max-w-md w-full mx-4">
                @csrf
                
                <!-- Team Logo Upload -->
                <div class="mb-5">
                    <label for="team_logo" class="block text-sm font-medium text-gray-300 mb-2">Team Logo</label>
                    <div class="flex items-center gap-4">
                        <div id="logoPreview" class="bg-[#2aa3ef20] p-4 rounded-xl w-20 h-20 flex items-center justify-center overflow-hidden">
                            <i class="hgi hgi-stroke hgi-group text-3xl text-[#2aa3ef]"></i>
                        </div>
                        <label for="team_logo" class="px-4 py-2 bg-[#2aa3ef20] hover:bg-[#2aa3ef30] border border-[#2aa3ef] text-[#2aa3ef] rounded-lg cursor-pointer transition">
                            Choose Image
                            <input type="file" id="team_logo" name="team_logo" accept="image/*" class="hidden">
                        </label>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Recommended: Square image, max 2MB</p>
                </div>

                <div>
                    <label class="block text-white mb-2">Team Name</label>
                    <input type="text" name="team_name" required 
                        class="w-full bg-[#ffffff0a] border border-[#2aa3ef20] rounded-lg px-4 py-2 text-white focus:outline-none focus:border-[#2aa3ef]">
                </div>
                <div>
                    <label class="block text-white mb-2">Description</label>
                    <textarea name="team_desc" rows="3" 
                        class="w-full bg-[#ffffff0a] border border-[#2aa3ef20] rounded-lg px-4 py-2 text-white focus:outline-none focus:border-[#2aa3ef]"></textarea>
                </div>
                <div>
                    <label class="block text-white mb-2">Member Limit</label>
                    <input type="number" name="member_limit" value="5" min="2" max="50" required
                        class="w-full bg-[#ffffff0a] border border-[#2aa3ef20] rounded-lg px-4 py-2 text-white focus:outline-none focus:border-[#2aa3ef]">
                </div>
                <button type="submit" class="w-full py-2 bg-[#2aa3ef] hover:bg-[#2aa3efcc] text-white font-semibold rounded-xl transition">
                    Create Team
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Logo Preview
            document.getElementById('team_logo')?.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('logoPreview');
                        preview.innerHTML = `<img src="${e.target.result}" alt="Logo Preview" class="w-full h-full object-cover rounded-xl">`;
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
    
@endsection