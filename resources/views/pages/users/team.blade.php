@extends('components._layouts.home')
@section('content')
    <div>
        <!-- Search Bar -->
        <div class="relative">
            <input 
                type="text" 
                class="peer py-2.5 sm:py-3 px-4 ps-11 block w-full bg-gradient-to-l from-[#163F44] to-[#020C0D] border border-gray-600 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none" 
                placeholder="Search">
            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-4 peer-disabled:opacity-50 peer-disabled:pointer-events-none">
                <i class="hgi hgi-stroke hgi-search-01"></i>
            </div>
        </div>

        <section class="py-5">
            <h2 class="text-2xl font-bold text-white mb-6">Cari Tim yang Cocok untukmu!</h2>

            <!-- team container -->
            @if (empty($teams))
                <p class="text-gray-400 text-center">Tidak ada tim yang ditemukan.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($teams as $team)
                        <div class="bg-[#ffffff0a] backdrop-blur-md border border-[#2aa3ef20] p-5 rounded-2xl shadow-md text-white hover:scale-[1.02] hover:border-[#2aa3ef80] transition duration-300">
                            <!-- Header -->
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="bg-[#2aa3ef20] p-3 rounded-full">
                                        <img src="{{ $team['team_icon'] }}" alt="team-icon" class="rounded-xl object-cover object-center" width="32" height="32">
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
                            <p class="text-sm text-gray-300 mb-5">{{ $team['team_desc'] }}</p>

                            <!-- Join button -->
                            <button class="w-full py-2 bg-[#2aa3ef] hover:bg-[#2aa3efcc] text-white font-semibold rounded-xl transition cursor-pointer">
                                Join
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
    
@endsection