@extends('components._layouts.admin')
@section('content')
@php
    use Carbon\Carbon;
@endphp
<div class="container mx-auto px-4 py-6 bg-gradient-to-l from-[#163F44] to-[#020C0D] min-h-screen">
    <h1 class="text-3xl font-bold mb-6 text-[#ffffff]">Manage Events</h1>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-500/20 border border-green-500 rounded-lg text-green-400">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-500/20 border border-red-500 rounded-lg text-red-400">
            {{ session('error') }}
        </div>
    @endif

    <!-- Tabs -->
    <div class="mb-6 flex gap-4 border-b border-[#1CC8EE]/30">
        <button onclick="switchTab('seminar')" id="tab-seminar" class="tab-btn px-6 py-3 font-semibold text-[#1CC8EE] border-b-2 border-[#1CC8EE]">Seminar</button>
        <button onclick="switchTab('beasiswa')" id="tab-beasiswa" class="tab-btn px-6 py-3 font-semibold text-gray-400 hover:text-white">Beasiswa</button>
        <button onclick="switchTab('lomba')" id="tab-lomba" class="tab-btn px-6 py-3 font-semibold text-gray-400 hover:text-white">Lomba</button>
    </div>

    <!-- Seminar Tab -->
    <div id="content-seminar" class="tab-content">
        <div class="mb-4 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-[#1CC8EE]">Seminar Management</h2>
            <button onclick="openModal('seminar', 'create')" class="px-4 py-2 bg-[#1CC8EE] text-white rounded-lg hover:bg-[#1CC8EE]/80">+ Add Seminar</button>
        </div>
        <div class="rounded-xl p-6 bg-gradient-to-r from-[#122E32] to-[#0B1A1C]" style="box-shadow:0 4px 10px rgba(0, 224, 255, 0.15);">
            <table class="min-w-full text-sm text-white">
                <thead>
                    <tr class="border-b border-[#1CC8EE]">
                        <th class="py-2 px-4 text-left">Nama Seminar</th>
                        <th class="py-2 px-4 text-left">Tanggal</th>
                        <th class="py-2 px-4 text-left">Pembicara</th>
                        <th class="py-2 px-4 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($seminars ?? [] as $seminar)
                    <tr class="border-b border-gray-700">
                        <td class="py-2 px-4">{{ $seminar['nama_seminar'] ?? '-' }}</td>
                        <td class="py-2 px-4">{{ isset($seminar['tanggal_pelaksanaan']) ? Carbon::parse($seminar['tanggal_pelaksanaan'])->format('d M Y') : '-' }}</td>
                        <td class="py-2 px-4">{{ $seminar['pembicara'] ?? '-' }}</td>
                        <td class="py-2 px-4">
                            <!-- <button onclick="openModal('seminar', 'edit', {{ json_encode($seminar) }})" class="text-blue-400 hover:text-blue-300 mr-3">Edit</button> -->
                            <form action="{{ route('admin.events.delete.seminar', $seminar['seminar_id']) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Beasiswa Tab -->
    <div id="content-beasiswa" class="tab-content hidden">
        <div class="mb-4 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-[#1CC8EE]">Beasiswa Management</h2>
            <button onclick="openModal('beasiswa', 'create')" class="px-4 py-2 bg-[#1CC8EE] text-white rounded-lg hover:bg-[#1CC8EE]/80">+ Add Beasiswa</button>
        </div>
        <div class="rounded-xl p-6 bg-gradient-to-r from-[#122E32] to-[#0B1A1C]" style="box-shadow:0 4px 10px rgba(0, 224, 255, 0.15);">
            <table class="min-w-full text-sm text-white">
                <thead>
                    <tr class="border-b border-[#1CC8EE]">
                        <th class="py-2 px-4 text-left">Nama Beasiswa</th>
                        <th class="py-2 px-4 text-left">Jenjang</th>
                        <th class="py-2 px-4 text-left">Pemberi</th>
                        <th class="py-2 px-4 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($beasiswas ?? [] as $beasiswa)
                    <tr class="border-b border-gray-700">
                        <td class="py-2 px-4">{{ $beasiswa['nama_beasiswa'] ?? '-' }}</td>
                        <td class="py-2 px-4">{{ $beasiswa['jenjang_beasiswa'] ?? '-' }}</td>
                        <td class="py-2 px-4">{{ $beasiswa['pemberi_beasiswa'] ?? '-' }}</td>
                        <td class="py-2 px-4">
                            <!-- <button onclick="openModal('beasiswa', 'edit', {{ json_encode($beasiswa) }})" class="text-blue-400 hover:text-blue-300 mr-3">Edit</button> -->
                            <form action="{{ route('admin.events.delete.beasiswa', $beasiswa['beasiswa_id']) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Lomba Tab -->
    <div id="content-lomba" class="tab-content hidden">
        <div class="mb-4 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-[#1CC8EE]">Lomba Management</h2>
            <button onclick="openModal('lomba', 'create')" class="px-4 py-2 bg-[#1CC8EE] text-white rounded-lg hover:bg-[#1CC8EE]/80">+ Add Lomba</button>
        </div>
        <div class="rounded-xl p-6 bg-gradient-to-r from-[#122E32] to-[#0B1A1C]" style="box-shadow:0 4px 10px rgba(0, 224, 255, 0.15);">
            <table class="min-w-full text-sm text-white">
                <thead>
                    <tr class="border-b border-[#1CC8EE]">
                        <th class="py-2 px-4 text-left">Nama Lomba</th>
                        <th class="py-2 px-4 text-left">Tanggal</th>
                        <th class="py-2 px-4 text-left">Penyelenggara</th>
                        <th class="py-2 px-4 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lombas ?? [] as $lomba)
                    <tr class="border-b border-gray-700">
                        <td class="py-2 px-4">{{ $lomba['nama_lomba'] ?? '-' }}</td>
                        <td class="py-2 px-4">{{ isset($lomba['tanggal_pelaksanaan']) ? Carbon::parse($lomba['tanggal_pelaksanaan'])->format('d M Y') : '-' }}</td>
                        <td class="py-2 px-4">{{ $lomba['penyelenggara'] ?? '-' }}</td>
                        <td class="py-2 px-4">
                            <!-- <button onclick="openModal('lomba', 'edit', {{ json_encode($lomba) }})" class="text-blue-400 hover:text-blue-300 mr-3">Edit</button> -->
                            <form action="{{ route('admin.events.delete.lomba', $lomba['lomba_id']) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="eventModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-[#122E32] rounded-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto" style="box-shadow:0 4px 10px rgba(0, 224, 255, 0.15);">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-xl font-semibold text-[#1CC8EE]"></h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-white">✕</button>
        </div>
        <form id="eventForm" method="POST">
            @csrf
            <div id="formContent" class="space-y-4">
                <!-- Form fields will be dynamically inserted here -->
            </div>
            <div class="mt-6 flex gap-4 justify-end">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-[#1CC8EE] text-white rounded-lg hover:bg-[#1CC8EE]/80">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentType = 'seminar';
let currentMode = 'create';
let currentId = null;

function switchTab(type) {
    currentType = type;
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('text-[#1CC8EE]', 'border-[#1CC8EE]', 'border-b-2');
        el.classList.add('text-gray-400');
    });
    
    document.getElementById(`content-${type}`).classList.remove('hidden');
    const btn = document.getElementById(`tab-${type}`);
    btn.classList.remove('text-gray-400');
    btn.classList.add('text-[#1CC8EE]', 'border-b-2', 'border-[#1CC8EE]');
}

function openModal(type, mode, data = null) {
    currentType = type;
    currentMode = mode;
    currentId = data ? (data[`${type}_id`] || data.seminar_id || data.beasiswa_id || data.lomba_id) : null;
    
    const modal = document.getElementById('eventModal');
    const title = document.getElementById('modalTitle');
    const form = document.getElementById('eventForm');
    const content = document.getElementById('formContent');
    
    title.textContent = `${mode === 'create' ? 'Create' : 'Edit'} ${type.charAt(0).toUpperCase() + type.slice(1)}`;
    
    let action = '';
    if (mode === 'create') {
        action = `{{ route('admin.events.store.${type}') }}`;
    } else {
        action = `{{ url('/admin/events/${type}') }}/${currentId}`;
        form.innerHTML += '@method("PUT")';
    }
    form.action = action;
    
    // Generate form fields based on type
    content.innerHTML = generateFormFields(type, data);
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal() {
    document.getElementById('eventModal').classList.add('hidden');
    document.getElementById('eventModal').classList.remove('flex');
    document.getElementById('eventForm').reset();
}

function generateFormFields(type, data) {
    let fields = '';
    
    if (type === 'seminar') {
        fields = `
            <div>
                <label class="block text-white mb-2">Nama Seminar *</label>
                <input type="text" name="nama_seminar" value="${data?.nama_seminar || ''}" required class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-white mb-2">Tanggal Pelaksanaan *</label>
                    <input type="date" name="tanggal_pelaksanaan" value="${data?.tanggal_pelaksanaan || ''}" required class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">
                </div>
                <div>
                    <label class="block text-white mb-2">Lokasi</label>
                    <input type="text" name="lokasi" value="${data?.lokasi || ''}" class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-white mb-2">Mulai Pendaftaran *</label>
                    <input type="date" name="mulai_pendaftaran" value="${data?.mulai_pendaftaran || ''}" required class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">
                </div>
                <div>
                    <label class="block text-white mb-2">Akhir Pendaftaran *</label>
                    <input type="date" name="akhir_pendaftaran" value="${data?.akhir_pendaftaran || ''}" required class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">
                </div>
            </div>
            <div>
                <label class="block text-white mb-2">Pembicara</label>
                <input type="text" name="pembicara" value="${data?.pembicara || ''}" class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">
            </div>
            <div>
                <label class="block text-white mb-2">Deskripsi</label>
                <textarea name="deskripsi" rows="3" class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">${data?.deskripsi || ''}</textarea>
            </div>
            <div>
                <label class="block text-white mb-2">Link Pendaftaran</label>
                <input type="url" name="link_pendaftaran" value="${data?.link_pendaftaran || ''}" class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">
            </div>
        `;
    } else if (type === 'beasiswa') {
        fields = `
            <div>
                <label class="block text-white mb-2">Nama Beasiswa *</label>
                <input type="text" name="nama_beasiswa" value="${data?.nama_beasiswa || ''}" required class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">
            </div>
            <div>
                <label class="block text-white mb-2">Jenjang Beasiswa</label>
                <input type="text" name="jenjang_beasiswa" value="${data?.jenjang_beasiswa || ''}" class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-white mb-2">Mulai Pendaftaran *</label>
                    <input type="date" name="mulai_pendaftaran" value="${data?.mulai_pendaftaran || ''}" required class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">
                </div>
                <div>
                    <label class="block text-white mb-2">Akhir Pendaftaran *</label>
                    <input type="date" name="akhir_pendaftaran" value="${data?.akhir_pendaftaran || ''}" required class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">
                </div>
            </div>
            <div>
                <label class="block text-white mb-2">Pemberi Beasiswa</label>
                <input type="text" name="pemberi_beasiswa" value="${data?.pemberi_beasiswa || ''}" class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">
            </div>
            <div>
                <label class="block text-white mb-2">Syarat Beasiswa</label>
                <textarea name="syarat_beasiswa" rows="3" class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">${data?.syarat_beasiswa || ''}</textarea>
            </div>
            <div>
                <label class="block text-white mb-2">Benefit Beasiswa</label>
                <textarea name="benefit_beasiswa" rows="3" class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">${data?.benefit_beasiswa || ''}</textarea>
            </div>
            <div>
                <label class="block text-white mb-2">Link Pendaftaran</label>
                <input type="url" name="link_pendaftaran" value="${data?.link_pendaftaran || ''}" class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">
            </div>
        `;
    } else if (type === 'lomba') {
        fields = `
            <div>
                <label class="block text-white mb-2">Nama Lomba *</label>
                <input type="text" name="nama_lomba" value="${data?.nama_lomba || ''}" required class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-white mb-2">Tanggal Pelaksanaan *</label>
                    <input type="date" name="tanggal_pelaksanaan" value="${data?.tanggal_pelaksanaan || ''}" required class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">
                </div>
                <div>
                    <label class="block text-white mb-2">Lokasi</label>
                    <input type="text" name="lokasi" value="${data?.lokasi || ''}" class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-white mb-2">Mulai Pendaftaran *</label>
                    <input type="date" name="mulai_pendaftaran" value="${data?.mulai_pendaftaran || ''}" required class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">
                </div>
                <div>
                    <label class="block text-white mb-2">Akhir Pendaftaran *</label>
                    <input type="date" name="akhir_pendaftaran" value="${data?.akhir_pendaftaran || ''}" required class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-white mb-2">Kategori Lomba</label>
                    <input type="text" name="kategori_lomba" value="${data?.kategori_lomba || ''}" class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">
                </div>
                <div>
                    <label class="block text-white mb-2">Penyelenggara</label>
                    <input type="text" name="penyelenggara" value="${data?.penyelenggara || ''}" class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">
                </div>
            </div>
            <div>
                <label class="block text-white mb-2">Deskripsi</label>
                <textarea name="deskripsi" rows="3" class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">${data?.deskripsi || ''}</textarea>
            </div>
            <div>
                <label class="block text-white mb-2">Link Pendaftaran</label>
                <input type="url" name="link_pendaftaran" value="${data?.link_pendaftaran || ''}" class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">
            </div>
        `;
    }
    
    return fields;
}
</script>
@endsection
