@extends('components._layouts.home')
@section('content')

    <!-- container -->
    <div class="grid grid-cols-12 gap-8">

        <!-- left side -->
        <div class="col-span-12 md:col-span-8 space-y-8">
            <!-- Search Bar -->
            <form class="relative" method="GET" action="{{ route('posts.index') }}">
    
                <input 
                    type="text" 
                    name="search"
                    {{-- ▼ MODIFIKASI: Tambahkan 'pe-10' (padding-end) di sini ▼ --}}
                    class="peer py-2.5 sm:py-3 px-4 ps-11 pe-10 block w-full bg-gradient-to-l from-[#163F44] to-[#020C0D] border border-gray-600 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 text-white" 
                    placeholder="Cari postingan berdasarkan caption..."
                    value="{{ request('search') }}"
                >
                <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-4 peer-disabled:opacity-50 peer-disabled:pointer-events-none">
                    <i class="hgi hgi-stroke hgi-search-01"></i>
                </div>

                {{-- ▼ TAMBAHAN BARU: Tombol 'X' untuk Reset Pencarian ▼ --}}
                @if(request('search'))
                    <a 
                        href="{{ route('posts.index') }}" 
                        title="Hapus pencarian"
                        class="absolute inset-y-0 end-0 flex items-center pe-4 text-gray-400 hover:text-white"
                        style="font-size: 1.5rem; line-height: 1; text-decoration: none;"
                    >
                        &times;
                    </a>
                @endif
            
            </form>

            @if ($errors->any())
                <div x-data="{ show: true }" x-show="show" x-transition
                    class="flex items-center justify-between p-4 mb-4 rounded-lg" 
                    style="background-color: #4b1111; color: white;">
                    
                    <div>
                        <strong>Oops! Ada masalah:</strong>
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button @click="show = false" class="text-white text-2xl ml-4">&times;</button>
                </div>
            @endif

            @if (session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition
                    class="flex items-center justify-between p-4 mb-4 rounded-lg" 
                    style="background-color: #4b1111; color: white;">
                    
                    <span>{{ session('error') }}</span>
                    <button @click="show = false" class="text-white text-2xl ml-4">&times;</button>
                </div>
            @endif

            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition
                    class="flex items-center justify-between p-4 mb-4 rounded-lg" 
                    style="background-color: #114b11; color: white;">
                    
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="text-white text-2xl ml-4">&times;</button>
                </div>
            @endif

            <!-- Post Input Component -->
            <x-_ui.post-input />

            @if (empty($posts))
                <p class="text-gray-400 text-center">Belum ada postingan.</p>
            @else
            <!-- Post List -->
            <div id="postList" class="space-y-6">
                <!-- post card -->
                @foreach ($posts as $post)
                <div class="bg-[#2aa3ef07] p-6 rounded-2xl shadow-md text-white">
                    <div class="flex items-center mb-4">
                    <img src="{{ $post['user']['avatar_url']  ?? 'https://qdfotopajdiuailyeprh.supabase.co/storage/v1/object/public/avatars/default-user.jpg' }}" 
                        alt="{{ $post['user']['name'] ?? 'User' }}" 
                        class="w-10 h-10 rounded-full mr-3 object-cover">
                    <div>
                        <p class="font-semibold">{{ $post['user']['name'] ?? 'Anonim' }}</p>
                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($post['created_at'])->diffForHumans() }}</p>
                    </div>
                    </div>

                    <p class="text-gray-200 mb-3">{{ $post['caption'] }}</p>

                    @if (!empty($post['image_url']))
                        <img src="{{ $post['image_url']}}" 
                            alt="Post image" 
                            class="rounded-xl w-full object-cover object-center max-h-64 mb-3">
                    @endif

                    <div x-data="{ liked: false, likes: {{ $post['likes_count'] ?? 0 }}, showComment: false }" >
                        <div class="flex gap-8 text-sm text-gray-400 mt-3 items-center select-none">
                            <!-- Like -->
                            <button 
                                @click="liked = !liked; likes += liked ? 1 : -1"
                                class="flex items-center gap-1 transition cursor-pointer"
                            >
                                <i 
                                    class="hgi hgi-stroke hgi-favourite text-2xl"
                                    :class="liked ? ' text-red-500' : ' text-white'"
                                ></i>
                                <span x-text="likes"></span>
                            </button>

                            <!-- Comment -->
                            <button 
                                @click="showComment = !showComment"
                                class="flex items-center gap-1 transition cursor-pointer"
                            >
                                <i class="hgi hgi-stroke hgi-message-02 text-2xl text-white"></i>
                                <span>{{ $post['comments_count'] ?? 0 }}</span>
                            </button>

                            <!-- Share -->
                            <button 
                                @click="navigator.share ? navigator.share({ title: 'Post dari {{ $post['user']['name'] }}', url: '{{ url('/post/' . $post['post_id']) }}' }) : navigator.clipboard.writeText('{{ url('/post/' . $post['post_id']) }}')"
                                class="flex items-center gap-1 transition cursor-pointer"
                            >
                                <i class="hgi hgi-stroke hgi-sent text-2xl text-white"></i>
                            </button>
                        </div>
                        <!-- Kolom Komentar -->
                        <div x-show="showComment" x-cloak x-transition class="mt-3">
                            <textarea 
                                placeholder="Tulis komentar..." 
                                class="w-full bg-[#ffffff10] text-white rounded-xl p-3 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#2aa3ef]"
                            ></textarea>
                            <button 
                                class="mt-2 px-4 py-2 bg-[#2aa3ef] rounded-xl text-white text-sm hover:bg-[#2aa3efc9]"
                            >
                                Kirim
                            </button>
                        </div>
                    </div>

                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- right side -->
        <div class="col-span-12 md:col-span-4 space-y-6">
            @include('components._ui.teamRecommendation')
        </div>
    </div>
@endsection