    <div 
        class="h-screen w-70 flex flex-col justify-between transition-all duration-300 border-r-1 border-gray-600"
    >
    <div class=" flex flex-col gap-6">
        <!-- Logo -->
        <div class="p-4 flex items-center border-b border-gray-700">
            <button @click="open = !open" class="text-gray-400 hover:text-white cursor-pointer">
                <div class="flex items-center mx-5 gap-3">
                    <i class="hgi hgi-stroke hgi-chart-rose text-2xl"></i>
                    <p class="font-bold" x-show="open">YIHAA</p>
                </div>
            </button>
        </div>

        <!-- Menu -->
        <nav class="flex flex-col space-y-2 mx-6 text-white">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 hover:bg-[#2A587E] px-3 py-2 rounded-full {{ request()->routeIs('admin.dashboard') ? 'bg-[#2A587E]' : '' }}">
            <i class="hgi hgi-stroke hgi-chart-rose text-2xl"></i>
            <span x-show="open" class="font-semibold">Dashboard</span>
            </a>

            <a href="{{ route('admin.events.index') }}" class="flex items-center space-x-3 hover:bg-[#2A587E] px-3 py-2 rounded-full {{ request()->routeIs('admin.events.*') ? 'bg-[#2A587E]' : '' }}">
            <i class="hgi hgi-stroke hgi-calendar-02 text-2xl"></i>
            <span x-show="open">Event</span>
            </a>

            <a href="{{ route('admin.materials.index') }}" class="flex items-center space-x-3 hover:bg-[#2A587E] px-3 py-2 rounded-full relative {{ request()->routeIs('admin.materials.*') ? 'bg-[#2A587E]' : '' }}">
            <i class="hgi hgi-stroke hgi-book-edit text-2xl"></i>
            <span x-show="open">Modules</span>
            <span class="absolute left-5 top-1 bg-blue-500 text-xs font-bold rounded-full w-4 h-4 flex items-center justify-center" id="pending-count" style="display: none;">1</span>
            </a>

            <a href="#" class="flex items-center space-x-3 hover:bg-[#2A587E] px-3 py-2 rounded-full">
            <i class="hgi hgi-stroke hgi-user-group text-2xl"></i>
            <span x-show="open">Team</span>
            </a>

            <a href="#" class="flex items-center space-x-3 hover:bg-[#2A587E] px-3 py-2 rounded-full">
            <i class="hgi hgi-stroke hgi-user-edit-01 text-2xl"></i>
            <span x-show="open">Users</span>
            </a>
        </nav>
    </div>

        <!-- User -->
        <div class="p-4 flex items-center space-x-3 border-t border-gray-700">
            <img src="{{ asset('images/avatar.jpg') }}" class="w-10 h-10 rounded-full">
            <div x-show="open">
                <p class="font-semibold text-gray-400">Mamat</p>
                <p class="text-sm text-gray-400">Admin</p>
            </div>
        </div>
    </div>
