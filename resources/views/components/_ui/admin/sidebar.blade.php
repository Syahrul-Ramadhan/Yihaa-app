    <div class="fixed overflow-y-hidden h-screen w-70 flex flex-col justify-between transition-all duration-300 border-r-1 border-gray-600 scroll-auto"
        :class="open ? 'w-64' : 'w-20'">
        <div class=" flex flex-col gap-6">
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
            <nav class="flex flex-col space-y-2 mx-6 text-white">
                <a href="/dashboard"
                    class="flex items-center space-x-3 hover:bg-[#2A587E] px-3 py-2 rounded-full {{ request()->routeIs('admin.dashboard') ? 'bg-[#2A587E]' : '' }}">
                    <i class="hgi hgi-stroke hgi-chart-rose text-2xl"></i>
                    <span x-show="open" class="font-semibold">Dashboard</span>
                </a>

                <a href="/manage-event"
                    class="flex items-center space-x-3 hover:bg-[#2A587E] px-3 py-2 rounded-full {{ request()->routeIs('admin.events.*') ? 'bg-[#2A587E]' : '' }}">
                    <i class="hgi hgi-stroke hgi-calendar-02 text-2xl"></i>
                    <span x-show="open">Event</span>
                </a>

                <a href="/manage-material"
                    class="flex items-center space-x-3 hover:bg-[#2A587E] px-3 py-2 rounded-full relative {{ request()->routeIs('admin.materials.*') ? 'bg-[#2A587E]' : '' }}">
                    <i class="hgi hgi-stroke hgi-book-edit text-2xl"></i>
                    <span x-show="open">Material</span>
                    <span
                        class="absolute left-5 top-1 bg-blue-500 text-xs font-bold rounded-full w-4 h-4 flex items-center justify-center"
                        id="pending-count" style="display: none;">1</span>
                </a>

                <a href="/manage-user" class="flex items-center space-x-3 hover:bg-[#2A587E] px-3 py-2 rounded-full">
                    <i class="hgi hgi-stroke hgi-user-edit-01 text-2xl"></i>
                    <span x-show="open">Users</span>
                </a>
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
                    <a href="{{ route('admin.logout') }}"
                        class="flex items-center gap-3 px-3 py-2 hover:bg-neutral-900 rounded">
                        <i class="hgi hgi-stroke hgi-logout-square-01"></i>
                        <span class="text-sm">Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </div>