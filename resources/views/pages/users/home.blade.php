@extends('components._layouts.home')
@section('content')

<!-- container -->
<div class="grid grid-cols-12 gap-8">

    <!-- left side -->
    <div class="col-span-12 md:col-span-8 space-y-8">
        <!-- Search Bar -->
        <form class="relative" method="GET" action="{{ route('posts.index') }}">

            <input type="text" name="search" {{-- ▼ MODIFIKASI: Tambahkan 'pe-10' (padding-end) di sini ▼ --}}
                class="peer py-2.5 sm:py-3 px-4 ps-11 pe-10 block w-full bg-gradient-to-l from-[#163F44] to-[#020C0D] border border-gray-600 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 text-white"
                placeholder="Cari postingan berdasarkan caption..." value="{{ request('search') }}">
            <div
                class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-4 peer-disabled:opacity-50 peer-disabled:pointer-events-none">
                <i class="hgi hgi-stroke hgi-search-01"></i>
            </div>

            {{-- ▼ TAMBAHAN BARU: Tombol 'X' untuk Reset Pencarian ▼ --}}
            @if(request('search'))
            <a href="{{ route('posts.index') }}" title="Hapus pencarian"
                class="absolute inset-y-0 end-0 flex items-center pe-4 text-gray-400 hover:text-white"
                style="font-size: 1.5rem; line-height: 1; text-decoration: none;">
                <i class="hgi hgi-stroke hgi-cancel-01 text-lg"></i>
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
            <div class="bg-[#2aa3ef07] p-6 rounded-2xl shadow-md text-white"
                x-data="{ dropdownOpen: false, showDeleteModal: false }">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <img src="{{ $post['uploader_avatar']  ?? 'https://qdfotopajdiuailyeprh.supabase.co/storage/v1/object/public/avatars/default-user.jpg' }}"
                            alt="{{ $post['uploader_name'] ?? 'User' }}"
                            class="w-10 h-10 rounded-full mr-3 object-cover">
                        <div>
                            <p class="font-semibold">{{ $post['uploader_name'] ?? 'Anonim' }}</p>
                            <p class="text-xs text-gray-400">
                                {{ \Carbon\Carbon::parse($post['created_at'])->diffForHumans() }}</p>
                        </div>
                    </div>

                    {{-- Dropdown Menu (Titik Tiga) --}}
                    @if(session('role') === 'admin' || session('user_id') == $post['uploaded_by'])
                    <div class="relative">
                        <button @click="dropdownOpen = !dropdownOpen"
                            class="text-gray-400 hover:text-white p-2 cursor-pointer">
                            <i class="hgi hgi-stroke hgi-more-vertical text-xl"></i>
                        </button>

                        {{-- Dropdown Content --}}
                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-transition
                            class="absolute right-0 mt-2 w-48 bg-[#1a1a1a] rounded-lg shadow-lg border border-gray-700 z-10">
                            <button @click="showDeleteModal = true; dropdownOpen = false" type="button"
                                class="w-full text-left px-4 py-3 text-red-400 hover:bg-red-900/20 rounded-lg flex items-center gap-2 cursor-pointer">
                                <i class="hgi hgi-stroke hgi-delete-02"></i>
                                Hapus Post
                            </button>
                        </div>
                    </div>

                    {{-- Delete Confirmation Modal --}}
                    <div x-show="showDeleteModal" x-cloak x-transition @click.self="showDeleteModal = false"
                        style="display: none;"
                        class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50">
                        <div class="bg-gradient-to-br from-[#1a1a1a] to-[#0a0a0a] rounded-2xl p-8 max-w-md w-full mx-4 border border-red-500/30 shadow-2xl"
                            @click.stop>
                            {{-- Icon Warning --}}
                            <div class="flex justify-center mb-4">
                                <div class="w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center">
                                    <i class="hgi hgi-stroke hgi-alert-02 text-4xl text-red-500"></i>
                                </div>
                            </div>

                            {{-- Title --}}
                            <h3 class="text-2xl font-bold text-white text-center mb-2">
                                Hapus Postingan?
                            </h3>

                            {{-- Message --}}
                            <p class="text-gray-400 text-center mb-6">
                                Yakin mau hapus postingan ini? Tindakan ini tidak bisa dibatalkan.
                            </p>

                            {{-- Buttons --}}
                            <div class="flex gap-3">
                                <button @click="showDeleteModal = false"
                                    class="flex-1 px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-xl transition cursor-pointer">
                                    Batal
                                </button>
                                <form action="{{ route('posts.destroy', $post['post_id']) }}" method="POST"
                                    class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl transition cursor-pointer">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <p class="text-gray-200 mb-3">{{ $post['caption'] }}</p>

                @if (!empty($post['image_url']))
                <div x-data="{ showImage: false }" class="relative">
                    <!-- GAMBAR POST -->
                    <img src="{{ $post['image_url'] }}" alt="Post image"
                        class="rounded-xl w-full object-cover object-center max-h-64 mb-3 cursor-pointer"
                        @click="showImage = true">

                    <!-- POP-UP FULL IMAGE -->
                    <div x-show="showImage" x-cloak @click="showImage = false"
                        class="fixed inset-0 bg-black/70 flex items-center justify-center p-4 z-50 "
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90">
                        <img src="{{ $post['image_url'] }}" class="max-w-full max-h-full rounded-xl shadow-lg"
                            @click.stop>
                        <button @click="showImage = false" class="absolute top-6 right-6 text-white cursor-pointer">
                            <i class="hgi hgi-stroke hgi-cancel-01 text-2xl"></i>
                        </button>
                    </div>
                </div>
                @endif

                <div x-data="commentSystem({{ $post['post_id'] }})">
                    <div class="flex gap-8 text-sm text-gray-400 mt-3 items-center select-none">
                        <!-- Like -->
                        <div
                            x-data="likeSystem({{ $post['post_id'] }}, {{ $post['is_liked'] ? 'true' : 'false' }}, {{ $post['likes_count'] ?? 0 }})">
                            <button @click=" toggleLike" class="flex items-center gap-1 cursor-pointer">
                                <i class="hgi hgi-stroke hgi-favourite text-2xl"
                                    :class="liked ? 'text-red-500' : 'text-white'"></i>
                                <span x-text="likes"></span>
                            </button>
                        </div>

                        <!-- Comment -->
                        <button @click="openPopup" class="flex items-center gap-1 transition cursor-pointer">
                            <i class="hgi hgi-stroke hgi-message-02 text-2xl text-white"></i>
                            <span>{{ $post['comments_count'] ?? 0 }}</span>
                        </button>

                        <!-- Share -->
                        <button
                            @click="navigator.share ? navigator.share({ title: 'Post dari {{ $post['uploader_name'] }}', url: '{{ url('/post/' . $post['post_id']) }}' }) : navigator.clipboard.writeText('{{ url('/post/' . $post['post_id']) }}')"
                            class="flex items-center gap-1 transition cursor-pointer">
                            <i class="hgi hgi-stroke hgi-sent text-2xl text-white"></i>
                        </button>
                    </div>
                    <!-- =============================== -->
                    <!-- POP UP KOMENTAR -->
                    <!-- =============================== -->
                    <div x-show="open" x-cloak
                        class="fixed inset-0 bg-[#0000006b] bg-opacity-70 flex justify-center items-center z-50 text-gray-100"
                        x-transition>

                        <!-- TOMBOL CLOSE -->
                        <button @click="closePopup"
                            class="absolute top-6 right-6 text-white text-2xl hover:text-gray-300 transition z-50 cursor-pointer">
                            <i class="hgi hgi-stroke hgi-cancel-01"></i>
                        </button>

                        <div class="bg-[#020C0D] rounded-xl w-11/12 md:w-3/4 lg:w-2/3 h-[90vh] flex overflow-hidden">

                            <!-- ======================= -->
                            <!-- KIRI (GAMBAR POSTINGAN) -->
                            <!-- ======================= -->
                            <div class="w-1/2 bg-[#000000] flex justify-center items-center">
                                <img src="{{ $post['image_url'] }}" class="max-h-full max-w-full object-contain">
                            </div>

                            <!-- ======================= -->
                            <!-- KANAN (DETAIL + KOMENTAR) -->
                            <!-- ======================= -->
                            <div class="w-1/2 flex flex-col">

                                <!-- UPLOADER -->
                                <div class="flex pt-6 px-4 gap-3 items-center mb-4 border-b border-[#092124] pb-3">
                                    <img src="{{ $post['uploader_avatar'] }}"
                                        class="w-10 h-10 rounded-full object-cover">
                                    <span class="font-semibold text-md">{{ $post['uploader_name'] }}</span>
                                </div>

                                <div class="h-full overflow-y-auto">

                                    <!-- CAPTION -->
                                    <div class="flex flex-col pt-3 px-4 ">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $post['uploader_avatar'] }}"
                                                class="w-8 h-8 rounded-full object-cover">
                                            <span class="font-semibold text-sm">{{ $post['uploader_name'] }}</span>
                                        </div>
                                        <p class="pl-11 text-sm mb-0.5">{{ $post['caption'] }}</p>
                                        <p class="pl-11 text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($post['created_at'])->diffForHumans() }}
                                        </p>
                                    </div>

                                    <!-- ======================= -->
                                    <!-- DAFTAR KOMENTAR -->
                                    <!-- ======================= -->
                                    <div class="flex-1 overflow-y-auto space-y-4 pr-1">

                                        <template x-for="c in comments" :key="c.comment_id">
                                            <div class="flex gap-3">
                                                <div>
                                                    <!-- KOMEN -->
                                                    <div class="flex flex-col pt-3 px-4 ">
                                                        <!-- User -->
                                                        <div class="flex items-center gap-3">
                                                            <img :src="c.users.avatar_url"
                                                                class="w-8 h-8 rounded-full object-cover">

                                                            <span class="font-semibold text-sm"
                                                                x-text="c.users.name"></span>
                                                        </div>

                                                        <!-- Comment text -->
                                                        <p class="pl-11 text-sm mb-0.5" x-text="c.comment_text"></p>

                                                        <!-- Time -->
                                                        <p class="pl-11 text-xs text-gray-500"
                                                            x-text="formatTime(c.created_at)">
                                                        </p>
                                                    </div>

                                                    <!-- tombol lihat balasan -->
                                                    <!-- <button class="pl-15 text-xs text-blue-600"
                                                        @click="toggleReplies(c.comment_id)">
                                                        Lihat balasan
                                                    </button> -->

                                                    <!-- daftar balasan -->
                                                    <div x-show="c.showReplies" class="ml-4 mt-2 space-y-2">
                                                        <template x-for="r in c.replies" :key="r.comment_id">
                                                            <div>
                                                                <p class="font-semibold text-xs" x-text="r.user_name">
                                                                </p>
                                                                <p class="text-gray-600 text-xs"
                                                                    x-text="r.comment_text">
                                                                </p>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>

                                            </div>
                                        </template>

                                    </div>
                                </div>

                                <!-- ======================= -->
                                <!-- INPUT KOMENTAR -->
                                <!-- ======================= -->
                                <form @submit.prevent="sendComment" class="mt-3 flex gap-2 px-3 pb-4">
                                    <input type="text" x-model="newComment" class="flex-1 p-2 border rounded-lg"
                                        placeholder="Tulis komentar...">
                                    <button class="bg-blue-500 text-white px-4 py-2 rounded-lg cursor-pointer">
                                        Kirim
                                    </button>
                                </form>

                            </div>
                        </div>

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
<script>
function likeSystem(postId, isLikedDefault, initialLikes) {
    return {
        liked: isLikedDefault,
        likes: initialLikes,

        async toggleLike() {
            // optimistik UI update
            const prevLiked = this.liked;
            const prevLikes = this.likes;

            this.liked = !this.liked;
            this.likes += this.liked ? 1 : -1;

            try {
                const res = await fetch("{{ route('posts.like') }}", { // <-- pastikan route ini benar
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        post_id: postId
                    })
                });

                // jika response http error
                if (!res.ok) {
                    const text = await res.text();
                    console.error("HTTP error:", res.status, text);
                    // rollback UI
                    this.liked = prevLiked;
                    this.likes = prevLikes;
                    return;
                }

                const data = await res.json();

                // opsional: periksa payload dari server
                if (data.error) {
                    console.error("Server error:", data.error);
                    this.liked = prevLiked;
                    this.likes = prevLikes;
                }

            } catch (err) {
                console.error("Request failed:", err);
                // rollback UI
                this.liked = prevLiked;
                this.likes = prevLikes;
            }
        }
    }
}

function commentSystem(postId) {
    return {
        open: false,
        comments: [],
        newComment: "",

        openPopup() {
            this.open = true;
            this.fetchComments();
        },

        closePopup() {
            this.open = false;
        },

        async fetchComments() {
            const res = await fetch(`/comments/${postId}`);
            const data = await res.json();

            this.comments = data.comments;
        },

        async sendComment() {
            if (!this.newComment.trim()) return;

            const text = this.newComment;

            const res = await fetch(`/comments/add`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({
                    post_id: postId,
                    comment_text: text
                })
            });

            const data = await res.json();

            if (data.success) {
                this.newComment = "";
                this.fetchComments(); // refresh list
            }
        },

        toggleReplies(id) {
            const c = this.comments.find(x => x.comment_id === id);
            c.showReplies = !c.showReplies;
        },

        formatTime(datetime) {
            const date = new Date(datetime);
            const local = new Date(date.getTime() - (date.getTimezoneOffset() * 60000));
            const now = new Date();
            const diffMs = now - local;

            const diffSec = Math.floor(diffMs / 1000);
            const diffMin = Math.floor(diffSec / 60);
            const diffHour = Math.floor(diffMin / 60);
            const diffDay = Math.floor(diffHour / 24);

            if (diffSec < 60) return `${diffSec} seconds ago`;
            if (diffMin < 60) return `${diffMin} minutes ago`;
            if (diffHour < 24) return `${diffHour} hours ago`;
            return `${diffDay} days ago`;
        }
    }
}
</script>

@endsection