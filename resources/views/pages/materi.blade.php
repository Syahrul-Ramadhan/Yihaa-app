@extends('components._layouts.home')

@section('content')
<div class="grid grid-cols-12 gap-8">

    {{-- Bagian Kiri: Konten Materi --}}
    <div class="col-span-12 md:col-span-8 space-y-8">

        {{-- Form Tambah Materi --}}
        <div class="bg-gradient-to-r from-[#122E32] to-[#0B1A1C] p-6 rounded-2xl shadow-md text-white">
            <div class="flex items-center space-x-4 mb-4">
                <div class="w-10 h-10 rounded-full bg-gray-500"></div>
                <div>
                    <h2 class="text-lg font-semibold">Insert title here</h2>
                    <p class="text-gray-400 text-sm">Write your subject here . . .</p>
                </div>
            </div>

            <form id="materiForm" class="space-y-4">
                <input 
                    type="text" 
                    id="materiTitle"
                    placeholder="Title Materi" 
                    class="w-full bg-[#0F262A] text-white rounded-lg p-3 text-sm placeholder-gray-400 focus:ring-2 focus:ring-teal-500 border border-gray-600">

                <textarea 
                    id="materiDesc"
                    placeholder="Description" 
                    class="w-full bg-[#0F262A] text-white rounded-lg p-3 text-sm placeholder-gray-400 focus:ring-2 focus:ring-teal-500 border border-gray-600 h-24"></textarea>

                {{-- Upload & Preview --}}
                <div class="flex items-center justify-between">
                    <label for="fileInput" class="cursor-pointer bg-[#0F262A] hover:bg-[#15393F] border border-gray-600 text-sm text-gray-300 py-2 px-4 rounded-lg transition">
                        Upload File
                    </label>
                    <input type="file" id="fileInput" accept="image/*" class="hidden">

                    <button 
                        type="button" 
                        id="publishBtn"
                        class="bg-teal-500 hover:bg-teal-600 text-white text-sm font-medium py-2 px-4 rounded-lg transition">
                        Publish
                    </button>
                </div>

                {{-- Preview Area --}}
                <div id="filePreview" class="mt-4 hidden">
                    <p class="text-sm text-gray-300 mb-2">Preview:</p>
                    <img id="previewImage" src="" alt="Preview" class="rounded-lg max-h-60 object-cover border border-gray-700 hidden">
                    <p id="fileName" class="text-gray-400 text-sm"></p>
                </div>
            </form>
        </div>

        {{-- Daftar Materi --}}
        <div id="materiList" class="space-y-6">
            @foreach ($materials as $item)
                <div class="bg-gradient-to-r from-[#122E32] to-[#0B1A1C] p-6 rounded-2xl shadow-md text-white">
                    <div class="flex items-center space-x-3 mb-4">
                        @if (!empty($item['user']['avatar_url']))
                            <img src="{{ $item['user']['avatar_url'] }}" alt="Avatar" class="w-10 h-10 rounded-full object-cover">
                        @else
                            <div class="w-10 h-10 rounded-full bg-gray-500"></div>
                        @endif
                        <div>
                            <h3 class="font-semibold text-base">{{ $item['user']['name'] ?? 'Anonim' }}</h3>
                        </div>
                    </div>

                    <h2 class="text-lg font-semibold mb-3">{{ $item['tittle'] }}</h2>

                    @if (!empty($item['thumbnail_url']))
                        <img src="{{ $item['thumbnail_url'] }}" alt="Materi Image" class="w-full rounded-lg mb-3">
                    @endif

                    <p class="text-gray-300 text-sm">{{ $item['description'] }}</p>

                    @if (!empty($item['file_url']))
                        <a href="{{ $item['file_url'] }}" target="_blank" class="text-teal-400 text-sm mt-2 inline-block hover:underline">
                            READ MORE
                        </a>
                    @endif
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
    const publishBtn = document.getElementById('publishBtn');
    const materiList = document.getElementById('materiList');

    let uploadedFile = null;

    // Menampilkan preview saat file dipilih
    fileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            uploadedFile = file;
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

    // Fungsi publish (frontend only)
    publishBtn.addEventListener('click', () => {
        const title = document.getElementById('materiTitle').value.trim();
        const desc = document.getElementById('materiDesc').value.trim();

        if (!title || !desc) {
            alert('Isi judul dan deskripsi terlebih dahulu.');
            return;
        }

        const card = document.createElement('div');
        card.className = 'bg-gradient-to-r from-[#122E32] to-[#0B1A1C] p-6 rounded-2xl shadow-md text-white';

        let imageHTML = '';
        if (uploadedFile && uploadedFile.type.startsWith('image/')) {
            const imgSrc = previewImage.src;
            imageHTML = `<img src="${imgSrc}" alt="Uploaded Image" class="w-full rounded-lg mb-3">`;
        }

        card.innerHTML = `
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-gray-500"></div>
                <div>
                    <h3 class="font-semibold text-base">You</h3>
                </div>
            </div>
            <h2 class="text-lg font-semibold mb-3">${title}</h2>
            ${imageHTML}
            <p class="text-gray-300 text-sm">${desc}</p>
            <a href="#" class="text-teal-400 text-sm mt-2 inline-block hover:underline">READ MORE</a>
        `;

        materiList.prepend(card);

        // Reset form
        document.getElementById('materiForm').reset();
        filePreview.classList.add('hidden');
        previewImage.classList.add('hidden');
        fileName.textContent = '';
        uploadedFile = null;
    });
</script>
@endsection
