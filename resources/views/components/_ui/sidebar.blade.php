    <div 
        x-data="{ open: true }"
        class="h-screen flex flex-col justify-between transition-all duration-300 border-r-1 border-gray-600"
        :class="open ? 'w-64' : 'w-20'"
    >
        <!-- Logo -->
        <div class="p-4 flex items-center justify-between">
            <button @click="open = !open" class="text-gray-400 hover:text-white cursor-pointer">
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('images/logo.png') }}" class="w-8 h-8">
                    <span x-show="open" class="font-semibold text-xl">Yihaa</span>
                </div>
            </button>
        </div>

        <!-- Menu -->
        <nav class="flex flex-col space-y-2 mx-6">
            <a href="#" class="flex items-center space-x-3 hover:bg-neutral-900 px-3 py-2 rounded-full">
            <i class="hgi hgi-stroke hgi-home-03 text-2xl"></i>
            <span x-show="open" class="font-semibold">Home</span>
            </a>

            <a href="#" class="flex items-center space-x-3 hover:bg-neutral-900 px-3 py-2 rounded-full">
            <i class="hgi hgi-stroke hgi-search-01 text-2xl"></i>
            <span x-show="open">Search</span>
            </a>

            <a href="#" class="flex items-center space-x-3 hover:bg-neutral-900 px-3 py-2 rounded-full relative">
            <i class="hgi hgi-stroke hgi-calendar-favorite-01 text-2xl"></i>
            <span x-show="open">Event Hub</span>
            <span class="absolute left-5 top-1 bg-blue-500 text-xs font-bold rounded-full w-4 h-4 flex items-center justify-center">1</span>
            </a>

            <a href="#" class="flex items-center space-x-3 hover:bg-neutral-900 px-3 py-2 rounded-full">
            <i class="hgi hgi-stroke hgi-book-open-01 text-2xl"></i>
            <span x-show="open">Materi</span>
            </a>

            <a href="#" class="flex items-center space-x-3 hover:bg-neutral-900 px-3 py-2 rounded-full">
            <i class="hgi hgi-stroke hgi-user-group text-2xl"></i>
            <span x-show="open">Team Collaboration</span>
            </a>

            <a href="#" class="flex items-center space-x-3 hover:bg-neutral-900 px-3 py-2 rounded-full">
            <i class="hgi hgi-stroke hgi-ai-chat-02 text-2xl"></i>
            <span x-show="open">Mang AI</span>
            </a>

            <a href="#" class="flex items-center space-x-3 hover:bg-neutral-900 px-3 py-2 rounded-full">
            <i class="hgi hgi-stroke hgi-more-horizontal-circle-02 text-2xl"></i>
            <span x-show="open">More</span>
            </a>
        </nav>

        <!-- User -->
        <div class="p-4 flex items-center space-x-3 border-t border-gray-700">
            <img src="{{ asset('images/avatar.jpg') }}" class="w-10 h-10 rounded-full">
            <div x-show="open">
                <p class="font-semibold">John Anjay</p>
                <p class="text-sm text-gray-400">Student</p>
            </div>
        </div>
    </div>
