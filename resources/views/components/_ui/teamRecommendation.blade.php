@php
    $teams = $teams ?? [];
@endphp

<div x-data="{ expanded: false, selectedTeam: null, showModal: false }" 
     class="rounded-2xl p-6" 
     style="background: linear-gradient(135deg, #122E32 0%, #0B1A1C 100%); box-shadow:0 4px 10px rgba(0, 224, 255, 0.15); color:#FFFFFF;">
    
    <h3 class="text-lg font-semibold mb-4" style="color:#00E0FF;">Team Recommendations</h3>
    
    @if(empty($teams))
        <p class="text-center text-gray-400 py-4">No teams available</p>
    @else
        <div :class="expanded ? 'max-h-96 overflow-y-auto' : ''" class="space-y-2">
            @foreach ($teams as $idx => $team)
                <div x-show="expanded || {{ $idx }} < 10" 
                     x-transition
                     class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-[#ffffff0a] transition cursor-pointer"
                     @click="selectedTeam = {{ json_encode($team) }}; showModal = true">
                    <span class="flex items-center gap-3">
                        <span class="w-5 text-right" style="color:#B0C7CC;">{{ $idx + 1 }}.</span>
                        <span style="color:#FFFFFF;">{{ $team['team_name'] }}</span>
                    </span>
                    <span class="text-sm" style="color:#B0C7CC;">
                        {{ $team['member_count'] }}/{{ $team['member_limit'] }}
                    </span>
                </div>
            @endforeach
        </div>
        
        @if(count($teams) > 10)
            <div class="text-center mt-4">
                <button @click="expanded = !expanded" class="font-medium hover:underline" style="color:#00E0FF;">
                    <span x-text="expanded ? 'Show Less' : 'Show More'"></span>
                </button>
            </div>
        @endif
    @endif

    <!-- Team Detail Modal -->
    <div x-show="showModal" 
         x-cloak
         @click.self="showModal = false"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
         style="display: none;">
        <div @click.stop 
             x-transition
             class="bg-[#0D1517] rounded-2xl p-6 w-full max-w-md border border-[#2aa3ef30]">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-white">Team Details</h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-white">
                    <i class="hgi hgi-stroke hgi-cancel-01 text-2xl"></i>
                </button>
            </div>

            <template x-if="selectedTeam">
                <div class="space-y-4">
                    <!-- Team Logo -->
                    <div class="flex justify-center">
                        <div class="w-24 h-24 bg-[#2aa3ef20] rounded-2xl flex items-center justify-center overflow-hidden">
                            <template x-if="selectedTeam.team_logo">
                                <img :src="selectedTeam.team_logo" :alt="selectedTeam.team_name" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!selectedTeam.team_logo">
                                <i class="hgi hgi-stroke hgi-group text-5xl text-[#2aa3ef]"></i>
                            </template>
                        </div>
                    </div>

                    <!-- Team Name -->
                    <div class="text-center">
                        <h4 class="text-2xl font-bold text-white mb-2" x-text="selectedTeam.team_name"></h4>
                        <div class="flex items-center justify-center gap-2">
                            <span class="px-3 py-1 rounded-full text-sm"
                                  :class="selectedTeam.team_status === 'closed' ? 'bg-red-500/20 text-red-400' : 'bg-[#2aa3ef20] text-[#2aa3ef]'"
                                  x-text="selectedTeam.team_status === 'closed' ? 'Closed' : 'Open'">
                            </span>
                        </div>
                    </div>

                    <!-- Member Count -->
                    <div class="bg-[#ffffff0a] rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">Members</span>
                            <span class="text-white font-semibold" x-text="`${selectedTeam.member_count}/${selectedTeam.member_limit}`"></span>
                        </div>
                        <div class="mt-2 bg-gray-700 rounded-full h-2 overflow-hidden">
                            <div class="bg-[#2aa3ef] h-full transition-all duration-300" 
                                 :style="`width: ${(selectedTeam.member_count / selectedTeam.member_limit * 100)}%`"></div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <a :href="`/teams/${selectedTeam.team_id}`" 
                       class="block w-full px-4 py-3 bg-[#2aa3ef] hover:bg-[#2aa3efcc] text-white text-center font-semibold rounded-lg transition">
                        View Full Details
                    </a>
                </div>
            </template>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    
    /* Custom scrollbar untuk team list */
    .max-h-96::-webkit-scrollbar {
        width: 8px;
    }
    
    .max-h-96::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 4px;
    }
    
    .max-h-96::-webkit-scrollbar-thumb {
        background: rgba(42, 163, 239, 0.5);
        border-radius: 4px;
    }
    
    .max-h-96::-webkit-scrollbar-thumb:hover {
        background: rgba(42, 163, 239, 0.7);
    }
</style>

