@extends('components._layouts.home')

@section('content')
<div class="grid grid-cols-12 gap-8">

    {{-- Bagian Kiri: Konten Materi --}}
    <div class="col-span-12 md:col-span-8 space-y-8">

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
            <button @click="show = false" class="text-white text-2xl ml-4 cursor-pointer">&times;</button>
        </div>
        @endif

        {{-- Form Tambah Materi --}}
        <div class="bg-gradient-to-r from-[#122E32] to-[#0B1A1C] p-6 rounded-2xl shadow-md text-white"
            x-data="{ showAdd: false }">
            <div class="flex items-center space-x-4 mb-4">
                <img src="{{ session('avatar_url') ?? 'https://qdfotopajdiuailyeprh.supabase.co/storage/v1/object/public/avatars/default-user.jpg' }}"
                    alt="{{ session('user_name', 'User') }}" class="w-10 h-10 rounded-full object-cover">
                <div class="flex flex-col gap-1">
                    <h2 class="text-lg font-semibold">{{ session('user_name', 'User') }}</h2>
                    <p class="text-gray-400 text-sm">share your knowledge</p>
                </div>

            </div>
            <div class="flex justify-end">
                <button @click="showAdd = true"
                    class="flex text-center gap-2 px-4 py-2 bg-[#1CC8EE] text-white rounded-lg hover:bg-[#1CC8EE]/80 cursor-pointer"><i
                        class="hgi hgi-stroke hgi-add-01"></i> Upload Materi</button>
            </div>
            <!-- POPUP TAMBAH MATERIAL -->
            <div x-show="showAdd" x-cloak
                class="fixed inset-0 bg-black/40 flex items-center justify-center px-4 z-30 text-gray-800">
                <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6 max-h-120 overflow-y-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold ">Tambah Materi</h2>
                        <button @click="showAdd = false" class="text-gray-600 cursor-pointer">
                            <i class="hgi hgi-stroke hgi-cancel-01 text-xl"></i>
                        </button>
                    </div>

                    <form action="{{ route("materi.store") }}" method="POST" enctype="multipart/form-data"
                        class="space-y-4">
                        @csrf

                        <!-- JUDUL MATERI -->
                        <div>
                            <label class="text-sm font-medium">Judul Materi</label>
                            <input type="text" name="tittle" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300">
                        </div>

                        <!-- DESKRIPSI -->
                        <div>
                            <label class="text-sm font-medium">Deskripsi</label>
                            <textarea name="description" rows="3" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"></textarea>
                        </div>

                        <!-- FILE MATERI -->
                        <div x-data="{ preview: null }">
                            <label class="text-sm font-medium">File Materi</label>

                            <input type="file" name="file" accept="*/*" @change="
                                    const file = $event.target.files[0];
                                    preview = URL.createObjectURL(file);
                                " required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300">
                        </div>

                        <!-- GAMBAR MATERI -->
                        <div x-data="{ preview: null }">
                            <label class="text-sm font-medium">Gambar Materi</label>

                            <input type="file" name="image" accept="image/*" @change="
                                    const file = $event.target.files[0];
                                    preview = URL.createObjectURL(file);
                                " required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300">

                            <template x-if="preview">
                                <img :src="preview" class="mt-3 w-full h-48 object-cover rounded-lg border">
                            </template>
                        </div>


                        <!-- BUTTON -->
                        <div class="flex justify-end mt-4">
                            <button type="submit"
                                class="px-4 py-2 bg-[#38BDF8] text-white rounded-lg hover:bg-[#2ba6db] hover:text-[#E0F2FE] cursor-pointer">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Daftar Materi --}}
        <div id="materiList" class="space-y-6">
            @foreach ($materials as $item)
            <div class="bg-gradient-to-r from-[#122E32] to-[#0B1A1C] p-6 rounded-2xl shadow-md text-white"
                x-data="{ dropdownOpen: false, showDeleteModal: false, expanded: false }">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        @if (!empty($item['user']['avatar_url']))
                        <img src="{{ $item['user']['avatar_url'] }}" alt="Avatar"
                            class="w-10 h-10 rounded-full object-cover mr-3">
                        @else
                        <div class="w-10 h-10 rounded-full bg-gray-500 mr-3"></div>
                        @endif
                        <div>
                            <h3 class="font-semibold text-base">{{ $item['user']['name'] ?? 'Anonim' }}</h3>
                        </div>
                    </div>

                    {{-- Dropdown Menu (Three Dots) - Only for owner or admin --}}
                    @if (session('user_id') == $item['uploaded_by'] || session('user_role') === 'admin')
                    <div class="relative">
                        <button @click="dropdownOpen = !dropdownOpen"
                            class="text-gray-400 hover:text-white p-2 rounded-full hover:bg-neutral-800 transition cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z" />
                            </svg>
                        </button>

                        {{-- Dropdown Content --}}
                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 bg-neutral-900 border border-neutral-700 rounded-lg shadow-lg z-10"
                            style="display: none;">

                            {{-- DOWNLOAD MATERI --}}
                            @if(!empty($item['file_url']))
                            <a href="{{ $item['file_url'] }}" download
                                class="block px-4 py-2 text-sm text-blue-400 hover:bg-neutral-800 rounded-lg transition">
                                <i class="hgi hgi-stroke hgi-download-01 mr-2"></i> Download Materi
                            </a>
                            @endif

                            <button @click="showDeleteModal = true; dropdownOpen = false"
                                class="w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-neutral-800 rounded-lg transition cursor-pointer">
                                <i class="hgi hgi-stroke hgi-delete-02 mr-2"></i> Hapus Materi
                            </button>
                        </div>
                    </div>
                    @else
                    {{-- Jika bukan owner atau admin, tampilkan dropdown tidak ada hapus --}}
                    <div class="relative">
                        <button @click="dropdownOpen = !dropdownOpen"
                            class="text-gray-400 hover:text-white p-2 rounded-full hover:bg-neutral-800 transition cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z" />
                            </svg>
                        </button>

                        {{-- Dropdown Content --}}
                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 bg-neutral-900 border border-neutral-700 rounded-lg shadow-lg z-10"
                            style="display: none;">

                            {{-- DOWNLOAD MATERI --}}
                            @if(!empty($item['file_url']))
                            <a href="{{ $item['file_url'] }}" download
                                class="block px-4 py-2 text-sm text-blue-400 hover:bg-neutral-800 rounded-lg transition">
                                <i class="hgi hgi-stroke hgi-download-01 mr-2"></i> Download Materi
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                <h2 class="text-lg font-semibold mb-3">{{ $item['tittle'] }}</h2>

                @if (!empty($item['thumbnail_url']))
                <img src="{{ $item['thumbnail_url'] }}" alt="Materi Image" class="w-full rounded-lg mb-3">
                @endif

                {{-- Description with expand/collapse --}}
                <div class="text-gray-300 text-sm">
                    @php
                    $description = $item['description'];
                    $charLimit = 200; // Batas karakter sebelum perlu expand
                    $needsExpand = strlen($description) > $charLimit;
                    @endphp

                    @if ($needsExpand)
                    <p x-show="!expanded" class="mb-2">
                        {{ Str::limit($description, $charLimit) }}
                    </p>
                    <p x-show="expanded" x-cloak class="mb-2">
                        {{ $description }}
                    </p>
                    <button @click="expanded = !expanded"
                        class="text-teal-400 hover:text-teal-300 font-medium text-sm transition cursor-pointer">
                        <span x-show="!expanded">Lihat Lebih Banyak</span>
                        <span x-show="expanded" x-cloak>Lihat Lebih Sedikit</span>
                    </button>
                    @else
                    <p>{{ $description }}</p>
                    @endif
                </div>

                {{-- Delete Confirmation Modal --}}
                <div x-show="showDeleteModal" @click.self="showDeleteModal = false"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
                    style="display: none;">

                    <div @click.stop
                        class="bg-gradient-to-b from-[#1a1a1a] to-[#0a0a0a] border border-neutral-800 rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4">

                        {{-- Warning Icon --}}
                        <div class="flex justify-center mb-6">
                            <div class="w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                        </div>

                        {{-- Title --}}
                        <h3 class="text-2xl font-bold text-white text-center mb-3">Hapus Materi?</h3>

                        {{-- Message --}}
                        <p class="text-gray-400 text-center mb-8">
                            Materi ini akan dihapus secara permanen dan tidak dapat dikembalikan.
                        </p>

                        {{-- Action Buttons --}}
                        <div class="flex gap-3">
                            <button @click="showDeleteModal = false"
                                class="flex-1 px-6 py-3 bg-neutral-800 hover:bg-neutral-700 text-white rounded-xl font-medium transition cursor-pointer">
                                Batal
                            </button>

                            <button @click="deleteMaterial({{ $item['material_id'] }})"
                                class="flex-1 px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium transition cursor-pointer">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            @if ($materials->isEmpty())
            <p class="text-gray-400 text-center">Belum ada materi tersedia.</p>
            @endif
        </div>

    </div>

    {{-- Bagian Kanan: Komponen Search & Team Recommendation --}}
    <div class="col-span-12 md:col-span-4 space-y-6">
        @include('components._ui.searchBar')
        @include('components._ui.teamRecommendation')
    </div>
</div>

{{-- Script Preview dan Tambah Materi --}}
<script>
const fileInput = document.getElementById('fileInput');
const previewImage = document.getElementById('previewImage');
const filePreview = document.getElementById('filePreview');
const fileName = document.getElementById('fileName');

// Menampilkan preview saat file dipilih
fileInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (file) {
        filePreview.classList.remove('hidden');
        fileName.textContent = file.name;

        // Preview gambar jika file adalah image
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (event) => {
                previewImage.src = event.target.result;
                previewImage.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            previewImage.classList.add('hidden');
        }
    }
});

// Delete material function
function deleteMaterial(materialId) {
    // Get CSRF token from form
    const csrfToken = document.querySelector('input[name="_token"]').value;

    fetch(`/materi/${materialId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Gagal menghapus materi');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus materi');
        });
}
</script>
@endsection