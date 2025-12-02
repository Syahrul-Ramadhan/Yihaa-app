    <div class="fixed h-screen flex flex-col justify-between transition-all duration-300 border-r-1 border-gray-600 scroll-auto"
        :class="open ? 'w-64' : 'w-20'">
        <div class="flex flex-col gap-10">
            <!-- Logo -->
            <div class="mt-3 p-4 flex items-center">
                <button @click="open = !open" class="text-gray-400 hover:text-white cursor-pointer">
                    <div class="flex items-center space-x-2 px-2">
                        <img src="{{ Vite::asset('resources/images/logo2.png') }}" class="w-8 h-8">
                        <span x-show="open" class="font-bold text-xl">YIHAA</span>
                    </div>
                </button>
            </div>

            <!-- Menu -->
            <nav class="flex flex-col space-y-2 mx-6">
                <a href="home"
                    class="flex items-center space-x-3 hover:bg-neutral-900 px-3 py-2 rounded-full {{ request()->is('home') ? 'bg-neutral-800 text-white' : 'text-gray-400' }}">
                    <i class="hgi hgi-stroke hgi-home-03 text-2xl"></i>
                    <span x-show="open" class="font-semibold">Home</span>
                </a>

                <!-- <a href="#" class="flex items-center space-x-3 hover:bg-neutral-900 px-3 py-2 rounded-full text-gray-400">
                <i class="hgi hgi-stroke hgi-search-01 text-2xl"></i>
                <span x-show="open">Search</span>
            </a> -->

                <a href="seminar"
                    class="flex items-center space-x-3 hover:bg-neutral-900 px-3 py-2 rounded-full relative {{ request()->is('seminar') || request()->is('beasiswa') || request()->is('lomba') ? 'bg-neutral-800 text-white' : 'text-gray-400' }}">
                    <i class="hgi hgi-stroke hgi-calendar-favorite-01 text-2xl"></i>
                    <span x-show="open">Event Hub</span>
                </a>

                <a href="materi"
                    class="flex items-center space-x-3 hover:bg-neutral-900 px-3 py-2 rounded-full {{ request()->is('materi') ? 'bg-neutral-800 text-white' : 'text-gray-400' }}">
                    <i class="hgi hgi-stroke hgi-book-open-01 text-2xl"></i>
                    <span x-show="open">Materi</span>
                </a>

                <a href="teams"
                    class="flex items-center space-x-3 hover:bg-neutral-900 px-3 py-2 rounded-full {{ request()->is('teams') || request()->is('teams/*') ? 'bg-neutral-800 text-white' : 'text-gray-400' }}">
                    <i class="hgi hgi-stroke hgi-user-group text-2xl"></i>
                    <span x-show="open">Team Collab</span>
                </a>

                <!-- "More" button with a small popup. We use Alpine.js for simple interactivity.
                 - x-data: holds local state for the popup 'moreOpen'
                 - @click: toggles the popup
                 - @click.outside: closes the popup when clicking outside (makes it behave like a small popup)
                 This is beginner-friendly and avoids writing separate JS files. -->
                <div x-data="{ moreOpen: false }" class="relative">
                    <button @click.prevent="moreOpen = !moreOpen"
                        class="flex items-center space-x-3 hover:bg-neutral-900 px-3 py-2 rounded-full text-gray-400">
                        <i class="hgi hgi-stroke hgi-more-horizontal-circle-02 text-2xl"></i>
                        <span x-show="open">More</span>
                    </button>

                    <!-- Popup content: small rounded box with three icons linking to other pages -->
                    <div x-show="moreOpen" @click.outside="moreOpen = false" x-cloak
                        class="absolute bottom-12 left-0 w-44 bg-[#052425] border border-gray-700 rounded-lg shadow-lg p-2 space-y-2 z-50"
                        style="display:none">
                        <!-- Each link is an icon + small label. Update href to the correct routes. -->
                        <!-- Messages link with inline SVG icon -->
                        <a href="chat"
                            class="flex items-center gap-3 px-3 py-2 hover:bg-neutral-900 rounded {{ request()->is('chat') || request()->is('chat/*') ? 'bg-neutral-800 text-white' : 'text-gray-400' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                            </svg>
                            <span class="text-sm">Messages</span>
                        </a>

                        <!-- Notifications link with bell SVG -->
                        <a href="/notifikasi"
                            class="flex items-center gap-3 px-3 py-2 hover:bg-neutral-900 rounded {{ request()->is('notifikasi') ? 'bg-neutral-800 text-white' : 'text-gray-400' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0 1 18.6 14.6V11a6 6 0 1 0-12 0v3.6c0 .538-.214 1.055-.595 1.395L4 17h5m6 0a3 3 0 1 1-6 0h6z" />
                            </svg>
                            <span class="text-sm">Notifications</span>
                        </a>

                        <!-- Profile link with user SVG -->
                        <a href="/profile"
                            class="flex items-center gap-3 px-3 py-2 hover:bg-neutral-900 rounded {{ request()->is('profile') ? 'bg-neutral-800 text-white' : 'text-gray-400' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="1.5" />
                            </svg>
                            <span class="text-sm">Profile</span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- User -->
        <div class="relative" x-data="{ logoutOpen: false }">
            <div class="p-4 flex items-center space-x-3 border-t border-gray-700 cursor-pointer hover:bg-neutral-900 transition-all duration-200"
                @click="logoutOpen = !logoutOpen">
                <img src="{{  session('avatar_url') }}" class="w-10 h-10 rounded-full">
                <div x-show="open">
                    <p class="font-semibold">{{  session('user_name')}}</p>
                </div>
            </div>

            <div class="relative" x-show="logoutOpen">
                <div x-show="logoutOpen" @click.outside="logoutOpen = false" x-cloak
                    class="absolute bottom-16 left-6 w-44 bg-[#052425] border border-gray-700 rounded-lg shadow-lg p-2 space-y-2 z-50"
                    style="display:none">
                    <a href="{{ route('logout') }}"
                        class="flex items-center gap-3 px-3 py-2 hover:bg-neutral-900 rounded">
                        <i class="hgi hgi-stroke hgi-logout-square-01"></i>
                        <span class="text-sm">Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </div>