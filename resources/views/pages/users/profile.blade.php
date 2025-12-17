@extends('components._layouts.home')

@section('content')
<div class="min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <!-- Profile Header -->
        <div class="bg-gradient-to-r from-[#1a1a1a] to-[#252525] rounded-2xl p-8 mb-8 border border-gray-800">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-6">
                    <!-- Avatar -->
                    <div class="relative">
                        @if($user['avatar_url'])
                        <img src="{{ $user['avatar_url'] }}" alt="{{ $user['name'] }}"
                            class="w-32 h-32 rounded-full object-cover border-4 border-[#2aa3ef]">
                        @else
                        <div
                            class="w-32 h-32 rounded-full bg-gradient-to-br from-[#2aa3ef] to-[#1e7bb8] flex items-center justify-center border-4 border-[#2aa3ef]">
                            <span class="text-4xl font-bold">{{ strtoupper(substr($user['name'], 0, 1)) }}</span>
                        </div>
                        @endif
                        <div
                            class="absolute bottom-0 right-0 w-8 h-8 bg-green-500 rounded-full border-4 border-[#1a1a1a]">
                        </div>
                    </div>

                    <!-- User Info -->
                    <div>
                        <h1 class="text-3xl font-bold mb-2">{{ $user['name'] }}</h1>
                        <div class="flex items-center gap-3 mb-3">
                            <span class="px-4 py-1 bg-[#2aa3ef]/20 text-[#2aa3ef] rounded-full text-sm font-medium">
                                {{ $profile ? ucfirst($profile['user_type'] ?? 'mahasiswa') : 'Mahasiswa' }}
                            </span>
                            <span class="text-gray-400 text-sm">{{ $user['email'] }}</span>
                        </div>
                        @if($profile && $profile['university'])
                        <p class="text-gray-300 text-sm">
                            <i class="hgi hgi-stroke hgi-university mr-2"></i>{{ $profile['university'] }}
                            @if($profile['program_study'])
                            • {{ $profile['program_study'] }}
                            @endif
                        </p>
                        @endif
                    </div>
                </div>

                <!-- Edit Button -->
                <a href="{{ route('profile.edit') }}"
                    class="px-6 py-3 bg-[#2aa3ef] hover:bg-[#1e7bb8] text-white rounded-xl font-medium transition-all duration-200 flex items-center gap-2">
                    <i class="hgi hgi-stroke hgi-pencil-edit-02"></i>
                    Edit Profile
                </a>
            </div>

            <!-- Bio Section -->
            @if($profile && $profile['bio'])
            <div class="mt-6 pt-6 border-t border-gray-700">
                <h3 class="text-gray-400 text-sm font-medium mb-2">Bio</h3>
                <p class="text-gray-200 leading-relaxed">{{ $profile['bio'] }}</p>
            </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- My Activities -->
                <div class="bg-gradient-to-br from-[#163F44] to-[#0a2020] rounded-2xl p-6 border border-gray-700">
                    <h2 class="text-2xl font-bold mb-6 text-white">My Activities</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Forum Card -->
                        <a href="{{ route('posts.index') }}"
                            class="group bg-gradient-to-br from-[#1a3a3a] to-[#0a2020] rounded-xl p-6 border border-gray-700 hover:border-[#2aa3ef] transition-all duration-200 hover:scale-105">
                            <div class="flex items-center justify-between mb-4">
                                <div
                                    class="w-16 h-16 bg-[#2aa3ef]/20 rounded-lg flex items-center justify-center group-hover:bg-[#2aa3ef]/30 transition-all">
                                    <i class="hgi hgi-stroke hgi-message-02 text-3xl text-[#2aa3ef]"></i>
                                </div>
                                <span class="text-4xl font-bold text-[#2aa3ef]">{{ $activities['posts'] }}</span>
                            </div>
                            <h3 class="text-lg font-semibold mb-1 text-white">Forum Posts</h3>
                            <p class="text-gray-400 text-sm">Total posts created</p>
                        </a>

                        <!-- Event Hub Card -->
                        <a href="{{ route('seminar') }}"
                            class="group bg-gradient-to-br from-[#2a1a3a] to-[#150a2a] rounded-xl p-6 border border-gray-700 hover:border-purple-500 transition-all duration-200 hover:scale-105">
                            <div class="flex items-center justify-between mb-4">
                                <div
                                    class="w-16 h-16 bg-purple-500/20 rounded-lg flex items-center justify-center group-hover:bg-purple-500/30 transition-all">
                                    <i class="hgi hgi-stroke hgi-calendar-03 text-3xl text-purple-500"></i>
                                </div>
                                <span class="text-4xl font-bold text-purple-500">{{ $activities['events'] }}</span>
                            </div>
                            <h3 class="text-lg font-semibold mb-1 text-white">Event Hub</h3>
                            <p class="text-gray-400 text-sm">Events applied</p>
                        </a>

                        <!-- Materials Card -->
                        <a href="{{ route('materi.index') }}"
                            class="group bg-gradient-to-br from-[#1a3a2a] to-[#0a200a] rounded-xl p-6 border border-gray-700 hover:border-green-500 transition-all duration-200 hover:scale-105">
                            <div class="flex items-center justify-between mb-4">
                                <div
                                    class="w-16 h-16 bg-green-500/20 rounded-lg flex items-center justify-center group-hover:bg-green-500/30 transition-all">
                                    <i class="hgi hgi-stroke hgi-book-open-01 text-3xl text-green-500"></i>
                                </div>
                                <span class="text-4xl font-bold text-green-500">{{ $activities['materials'] }}</span>
                            </div>
                            <h3 class="text-lg font-semibold mb-1 text-white">Materials</h3>
                            <p class="text-gray-400 text-sm">Materials uploaded</p>
                        </a>

                        <!-- Team Collaboration Card -->
                        <a href="{{ route('teams.index') }}"
                            class="group bg-gradient-to-br from-[#3a2a1a] to-[#2a1a0a] rounded-xl p-6 border border-gray-700 hover:border-orange-500 transition-all duration-200 hover:scale-105">
                            <div class="flex items-center justify-between mb-4">
                                <div
                                    class="w-16 h-16 bg-orange-500/20 rounded-lg flex items-center justify-center group-hover:bg-orange-500/30 transition-all">
                                    <i class="hgi hgi-stroke hgi-user-group text-3xl text-orange-500"></i>
                                </div>
                                <span class="text-4xl font-bold text-orange-500">{{ $activities['teams'] }}</span>
                            </div>
                            <h3 class="text-lg font-semibold mb-1 text-white">Team Collaboration</h3>
                            <p class="text-gray-400 text-sm">Teams joined</p>
                        </a>
                    </div>
                </div>

            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Peran dalam Tim -->
                @if($profile && $profile['role_in_team'])
                <div class="bg-gradient-to-br from-[#163F44] to-[#0a2020] rounded-2xl p-6 border border-gray-700">
                    <h2 class="text-xl font-bold mb-4 text-white">Peran dalam Tim</h2>
                    <div
                        class="bg-gradient-to-r from-[#2aa3ef]/20 to-purple-500/20 rounded-xl p-4 border border-[#2aa3ef]/30">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#2aa3ef] rounded-lg flex items-center justify-center">
                                <i class="hgi hgi-stroke hgi-tie text-white"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400">Current Role</p>
                                <p class="font-semibold">{{ $profile['role_in_team'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Team Collaboration List -->
                <div class="bg-gradient-to-br from-[#163F44] to-[#0a2020] rounded-2xl p-6 border border-gray-700">
                    <h2 class="text-xl font-bold mb-4 text-white">Team Collaboration</h2>

                    @if(count($teams) > 0)
                    <div class="space-y-3">
                        @foreach($teams as $team)
                        <a href="{{ route('teams.show', $team['team_id']) }}"
                            class="flex items-center gap-3 p-3 bg-gradient-to-r from-[#1a3a3a] to-[#0a2020] hover:from-[#2a4a4a] hover:to-[#1a3030] rounded-xl border border-gray-700 hover:border-[#2aa3ef] transition-all duration-200">
                            @if($team['team_logo'])
                            <img src="{{ $team['team_logo'] }}" alt="{{ $team['team_name'] }}"
                                class="w-12 h-12 rounded-lg object-cover">
                            @else
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-[#2aa3ef] to-purple-500 rounded-lg flex items-center justify-center">
                                <i class="hgi hgi-stroke hgi-user-group text-white"></i>
                            </div>
                            @endif
                            <div class="flex-1">
                                <h3 class="font-semibold text-sm text-white">{{ $team['team_name'] }}</h3>
                                <p class="text-xs text-gray-400">{{ $team['role'] }}</p>
                            </div>
                            <i class="hgi hgi-stroke hgi-arrow-right-01 text-gray-500"></i>
                        </a>
                        @endforeach
                    </div>

                    @if(count($teams) >= 3)
                    <a href="{{ route('teams.index') }}"
                        class="block text-center mt-4 text-[#2aa3ef] hover:text-[#1e7bb8] text-sm font-medium">
                        View All Teams <i class="hgi hgi-stroke hgi-arrow-right-01 ml-1"></i>
                    </a>
                    @endif
                    @else
                    <div class="text-center py-8">
                        <i class="hgi hgi-stroke hgi-user-group text-4xl text-gray-600 mb-3"></i>
                        <p class="text-gray-400 text-sm mb-3">Belum bergabung dengan tim</p>
                        <a href="{{ route('teams.index') }}"
                            class="inline-block px-4 py-2 bg-[#2aa3ef] hover:bg-[#1e7bb8] text-white rounded-lg text-sm font-medium transition-all">
                            Explore Teams
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection