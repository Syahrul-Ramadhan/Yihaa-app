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
        <button onclick="switchTab('seminar')" id="tab-seminar"
            class="tab-btn px-6 py-3 font-semibold text-[#1CC8EE] border-b-2 border-[#1CC8EE] cursor-pointer">Seminar</button>
        <button onclick="switchTab('beasiswa')" id="tab-beasiswa"
            class="tab-btn px-6 py-3 font-semibold text-gray-400 hover:text-white cursor-pointer">Beasiswa</button>
        <button onclick="switchTab('lomba')" id="tab-lomba"
            class="tab-btn px-6 py-3 font-semibold text-gray-400 hover:text-white cursor-pointer">Lomba</button>
    </div>

    <!-- Seminar Tab -->
    <div id="content-seminar" class="tab-content">
        <div class="mb-4 flex justify-between items-center" x-data="{ showAdd: false }">
            <h2 class="text-xl font-semibold text-[#1CC8EE]">Seminar Management</h2>
            <button @click="showAdd = true"
                class="flex text-center gap-2 px-4 py-2 bg-[#1CC8EE] text-white rounded-lg hover:bg-[#1CC8EE]/80 cursor-pointer">
                <i class="hgi hgi-stroke hgi-add-01"></i>
                Add Seminar
            </button>
            <!-- POPUP TAMBAH SEMINAR -->
            <div x-show="showAdd" x-cloak
                class="fixed inset-0 bg-black/40 flex items-center justify-center px-4 z-30 text-gray-800">
                <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6 max-h-120 overflow-y-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold ">Tambah Seminar</h2>
                        <button @click="showAdd = false" class="text-gray-600 cursor-pointer">
                            <i class="hgi hgi-stroke hgi-cancel-01 text-xl"></i>
                        </button>
                    </div>

                    <form action="{{ route('seminar.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-4">
                        @csrf

                        <!-- NAMA SEMINAR -->
                        <div>
                            <label class="text-sm font-medium">Nama Seminar</label>
                            <input type="text" name="nama" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300">
                        </div>

                        <!-- TANGGAL PELAKSANAAN -->
                        <div>
                            <label class="text-sm font-medium">Tanggal Pelaksanaan</label>
                            <input type="date" name="tanggal_pelaksanaan" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300">
                        </div>

                        <!-- TANGGAL MULAI PENDAFTARAN -->
                        <div>
                            <label class="text-sm font-medium">Mulai Pendaftaran</label>
                            <input type="date" name="mulai_pendaftaran" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300">
                        </div>

                        <!-- TANGGAL AKHIR PENDAFTARAN -->
                        <div>
                            <label class="text-sm font-medium">Akhir Pendaftaran</label>
                            <input type="date" name="akhir_pendaftaran" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300">
                        </div>

                        <!-- LOKASI -->
                        <div>
                            <label class="text-sm font-medium">Lokasi</label>
                            <input type="text" name="lokasi" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300">
                        </div>

                        <!-- PEMBICARA -->
                        <div>
                            <label class="text-sm font-medium">Pembicara</label>
                            <input type="text" name="pembicara" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300">
                        </div>

                        <!-- DESKRIPSI -->
                        <div>
                            <label class="text-sm font-medium">Deskripsi</label>
                            <textarea name="deskripsi" rows="3" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"></textarea>
                        </div>

                        <!-- LINK PENDAFTARAN -->
                        <div>
                            <label class="text-sm font-medium">Link Pendaftaran</label>
                            <input type="url" name="link" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300">
                        </div>

                        <!-- GAMBAR SEMINAR -->
                        <div x-data="{ preview: null }">
                            <label class="text-sm font-medium">Gambar Seminar</label>

                            <input type="file" name="gambar" accept="image/*" @change="
                                    const file = $event.target.files[0];
                                    preview = URL.createObjectURL(file);
                                "
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
        <div class="rounded-xl p-6 bg-gradient-to-r from-[#122E32] to-[#0B1A1C]" x-data="{showEdit: false, showDelete: false, deleteId: null, editData: {
                seminar_id: null,
                nama_seminar: '',
                tanggal_pelaksanaan: '',
                mulai_pendaftaran: '',
                akhir_pendaftaran: '',
                lokasi: '',
                pembicara: '',
                deskripsi: '',
                link_pendaftaran: '',
                image_url: ''
            } }" style=" box-shadow:0 4px 10px rgba(0, 224, 255, 0.15);">
            <table class="min-w-full text-sm text-white">
                <thead>
                    <tr class="border-b border-[#1CC8EE]">
                        <th class="py-2 px-4 text-left">Nama Seminar</th>
                        <th class="py-2 px-4 text-center">Tanggal</th>
                        <th class="py-2 px-4 text-center">Pembicara</th>
                        <th class="py-2 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($seminars ?? [] as $seminar)
                    <tr class="border-b border-gray-700">
                        <td class="py-2 px-4">{{ $seminar['nama_seminar'] ?? '-' }}</td>
                        <td class="py-2 px-4 text-center">
                            {{ isset($seminar['tanggal_pelaksanaan']) ? Carbon::parse($seminar['tanggal_pelaksanaan'])->format('d M Y') : '-' }}
                        </td>
                        <td class="py-2 px-4 text-center">{{ $seminar['pembicara'] ?? '-' }}</td>
                        <td class="py-3 text-center">
                            <div class="flex justify-center gap-2">

                                <!-- EDIT -->
                                <button @click="
                                    showEdit = true;
                                    editData.seminar_id = '{{ $seminar['seminar_id'] }}';
                                    editData.nama_seminar = '{{ $seminar['nama_seminar'] }}';
                                    editData.tanggal_pelaksanaan = '{{ $seminar['tanggal_pelaksanaan'] }}';
                                    editData.mulai_pendaftaran = '{{ $seminar['mulai_pendaftaran'] }}';
                                    editData.akhir_pendaftaran = '{{ $seminar['akhir_pendaftaran'] }}';
                                    editData.lokasi = '{{ $seminar['lokasi'] }}';
                                    editData.pembicara = '{{ $seminar['pembicara'] }}';
                                    editData.deskripsi = '{{ $seminar['deskripsi'] }}';
                                    editData.link_pendaftaran = '{{ $seminar['link_pendaftaran'] }}';
                                    editData.image_url = '{{ $seminar['image_url'] ?? '' }}';"
                                    class="px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs cursor-pointer">
                                    Edit
                                </button>

                                <!-- DELETE -->
                                <button @click="
                                    deleteId = '{{ $seminar['seminar_id'] }}';
                                    editData.image_url = '{{ $seminar['image_url'] ?? '' }}';
                                    showDelete = true;
                                                        "
                                    class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-xs cursor-pointer">
                                    Hapus
                                </button>

                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- POPUP EDIT SEMINAR -->
            <div x-show="showEdit" x-cloak
                class="fixed inset-0 bg-black/40 flex items-center justify-center px-4 z-30 text-gray-800">
                <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6 max-h-120 overflow-y-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold ">Edit Seminar</h2>
                        <button @click="showEdit = false" class="text-gray-600 cursor-pointer">
                            <i class="hgi hgi-stroke hgi-cancel-01 text-xl"></i>
                        </button>
                    </div>

                    <!-- POP UP EDIT -->
                    <form action="{{ route('seminar.update') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-4">
                        @csrf

                        <input type="hidden" name="id" :value="editData.seminar_id">

                        <div>
                            <label class="text-sm font-medium">Nama Seminar</label>
                            <input type="text" name="nama" x-model="editData.nama_seminar"
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2">
                        </div>

                        <div>
                            <label class="text-sm font-medium">Tanggal Pelaksanaan</label>
                            <input type="date" name="tanggal_pelaksanaan" x-model="editData.tanggal_pelaksanaan"
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2">
                        </div>

                        <div>
                            <label class="text-sm font-medium">Mulai Pendaftaran</label>
                            <input type="date" name="mulai_pendaftaran" x-model="editData.mulai_pendaftaran"
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2">
                        </div>

                        <div>
                            <label class="text-sm font-medium">Akhir Pendaftaran</label>
                            <input type="date" name="akhir_pendaftaran" x-model="editData.akhir_pendaftaran"
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2">
                        </div>

                        <div>
                            <label class="text-sm font-medium">Lokasi</label>
                            <input type="text" name="lokasi" x-model="editData.lokasi"
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2">
                        </div>

                        <div>
                            <label class="text-sm font-medium">Pembicara</label>
                            <input type="text" name="pembicara" x-model="editData.pembicara"
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2">
                        </div>

                        <div>
                            <label class="text-sm font-medium">Deskripsi</label>
                            <textarea name="deskripsi" rows="3" x-model="editData.deskripsi"
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2"></textarea>
                        </div>

                        <div>
                            <label class="text-sm font-medium">Link Pendaftaran</label>
                            <input type="url" name="link" x-model="editData.link_pendaftaran"
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2">
                        </div>

                        <div>
                            <label class="text-sm font-medium">Gambar Seminar (opsional)</label>
                            <input type="file" name="gambar"
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2">
                        </div>

                        <!-- untuk menyimpan gambar lama -->
                        <input type="hidden" name="old_gambar" :value="editData.image_url">

                        <div class="flex justify-end mt-4">
                            <button type="submit"
                                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 cursor-pointer">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>


                </div>
            </div>
            <!-- POPUP DELETE -->
            <div x-show="showDelete" x-cloak
                class="fixed inset-0 bg-black/40 flex items-center justify-center px-4 z-30">
                <div class="bg-white w-full max-w-sm rounded-xl shadow-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-800">Hapus Seminar?</h2>
                    <p class="text-sm text-gray-600 mt-2">
                        Seminar yang dihapus tidak dapat dikembalikan.
                    </p>

                    <div class="flex justify-end gap-3 mt-6">
                        <button @click="showDelete = false"
                            class="px-4 py-2 border-gray-300 text-gray-800 hover:bg-gray-200 rounded-lg border cursor-pointer">Batal</button>

                        <form action="{{ route('seminar.delete') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" :value="deleteId">
                            <input type="hidden" name="image_url" :value="editData.image_url">
                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Beasiswa Tab -->
    <div id="content-beasiswa" class="tab-content hidden">
        <div class="mb-4 flex justify-between items-center" x-data="{ showAdd: false }">
            <h2 class="text-xl font-semibold text-[#1CC8EE]">Beasiswa Management</h2>
            <button @click="showAdd = true"
                class="flex text-center gap-2 px-4 py-2 bg-[#1CC8EE] text-white rounded-lg hover:bg-[#1CC8EE]/80 cursor-pointer">
                <i class="hgi hgi-stroke hgi-add-01"></i>
                Add Beasiswa
            </button>

            <!-- POPUP TAMBAH BEASISWA -->
            <div x-show="showAdd" x-cloak
                class="fixed inset-0 bg-black/40 flex items-center justify-center px-4 z-30 text-gray-800">
                <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6 max-h-120 overflow-y-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold ">Tambah Beasiswa</h2>
                        <button @click="showAdd = false" class="text-gray-600 cursor-pointer">
                            <i class="hgi hgi-stroke hgi-cancel-01 text-xl"></i>
                        </button>
                    </div>

                    <form action="{{ route('beasiswa.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-4">
                        @csrf

                        <!-- NAMA BEASISWA -->
                        <div>
                            <label class="text-sm font-medium">Nama Beasiswa</label>
                            <input type="text" name="nama" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300">
                        </div>

                        <!-- JENJANG BEASISWA -->
                        <div>
                            <label class="text-sm font-medium">Jenjang Beasiswa</label>
                            <textarea name="jenjang" rows="3" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"></textarea>
                        </div>

                        <!-- TANGGAL MULAI PENDAFTARAN -->
                        <div>
                            <label class="text-sm font-medium">Mulai Pendaftaran</label>
                            <input type="date" name="mulai_pendaftaran" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300">
                        </div>

                        <!-- TANGGAL AKHIR PENDAFTARAN -->
                        <div>
                            <label class="text-sm font-medium">Akhir Pendaftaran</label>
                            <input type="date" name="akhir_pendaftaran" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300">
                        </div>

                        <!-- SYARAT BEASISWA -->
                        <div>
                            <label class="text-sm font-medium">Syarat Beasiswa</label>
                            <textarea name="syarat" rows="3" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"></textarea>
                        </div>

                        <!-- BENEFIT BEASISWA -->
                        <div>
                            <label class="text-sm font-medium">Benefit Beasiswa</label>
                            <textarea name="benefit" rows="3" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"></textarea>
                        </div>

                        <!-- PEMBERI BEASISWA -->
                        <div>
                            <label class="text-sm font-medium">Pemberi Beasiswa</label>
                            <input type="text" name="pemberi" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"></input>
                        </div>

                        <!-- LINK PENDAFTARAN -->
                        <div>
                            <label class="text-sm font-medium">Link Pendaftaran</label>
                            <input type="url" name="link" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300">
                        </div>

                        <!-- GAMBAR BEASISWA -->
                        <div x-data="{ preview: null }">
                            <label class="text-sm font-medium">Gambar Beasiswa (opsional)</label>
                            <input type="file" name="gambar" accept="image/*" @change="
                                    const file = $event.target.files[0];
                                    preview = URL.createObjectURL(file);
                                "
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
        <div class="rounded-xl p-6 bg-gradient-to-r from-[#122E32] to-[#0B1A1C]" x-data="{showEdit: false, showDelete: false, deleteId: null, editData: {
                beasiswa_id: null,
                nama_beasiswa: '',
                jenjang_beasiswa: '',
                mulai_pendaftaran: '',
                akhir_pendaftaran: '',
                syarat_beasiswa: '',
                benefit_beasiswa: '',
                pemberi_beasiswa: '',
                link_pendaftaran: '',
                image_url: ''
            } }" style="box-shadow:0 4px 10px rgba(0, 224, 255, 0.15);">
            <table class="min-w-full text-sm text-white">
                <thead>
                    <tr class="border-b border-[#1CC8EE]">
                        <th class="py-2 px-4 text-left">Nama Beasiswa</th>
                        <th class="py-2 px-4 text-center">Jenjang</th>
                        <th class="py-2 px-4 text-center">Pemberi</th>
                        <th class="py-2 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($beasiswas ?? [] as $beasiswa)
                    <tr class="border-b border-gray-700">
                        <td class="py-2 px-4">{{ $beasiswa['nama_beasiswa'] ?? '-' }}</td>
                        <td class="py-2 px-4 text-center">{{ $beasiswa['jenjang_beasiswa'] ?? '-' }}</td>
                        <td class="py-2 px-4 text-center">{{ $beasiswa['pemberi_beasiswa'] ?? '-' }}</td>
                        <td class="py-2 px-4 text-center">
                            <div class="flex justify-center gap-2">

                                <!-- EDIT -->
                                <button @click="
                                    showEdit = true;
                                    editData.id = '{{ $beasiswa['beasiswa_id'] }}';
                                    editData.nama_beasiswa = '{{ $beasiswa['nama_beasiswa'] }}';
                                    editData.jenjang_beasiswa = '{{ $beasiswa['jenjang_beasiswa'] }}';
                                    editData.mulai_pendaftaran = '{{ $beasiswa['mulai_pendaftaran'] }}';
                                    editData.akhir_pendaftaran = '{{ $beasiswa['akhir_pendaftaran'] }}';
                                    editData.syarat_beasiswa = '{{ $beasiswa['syarat_beasiswa'] }}';
                                    editData.benefit_beasiswa = '{{ $beasiswa['benefit_beasiswa'] }}';
                                    editData.pemberi_beasiswa = '{{ $beasiswa['pemberi_beasiswa'] }}';
                                    editData.link_pendaftaran = '{{ $beasiswa['link_pendaftaran'] }}';
                                    editData.image_url = '{{ $beasiswa['image_url'] ?? '' }}';"
                                    class="px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs cursor-pointer">
                                    Edit
                                </button>

                                <!-- DELETE -->
                                <button @click="
                                    deleteId = '{{ $beasiswa['beasiswa_id'] }}';
                                    editData.image_url = '{{ $beasiswa['image_url'] ?? '' }}';
                                    showDelete = true;"
                                    class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-xs cursor-pointer">
                                    Hapus
                                </button>

                            </div>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- POPUP EDIT BEASISWA -->
            <div x-show="showEdit" x-cloak
                class="fixed inset-0 bg-black/40 flex items-center justify-center px-4 z-30 text-gray-700">
                <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6 max-h-120 overflow-y-auto">

                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">Edit Beasiswa</h2>
                        <button @click="showEdit = false" class="text-gray-600 cursor-pointer">
                            <i class="hgi hgi-stroke hgi-cancel-01 text-xl"></i>
                        </button>
                    </div>

                    <form action="{{ route('beasiswa.update') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-4">
                        @csrf

                        <input type="hidden" name="id" x-model="editData.id">

                        <div>
                            <label class="text-sm font-medium">Nama Beasiswa</label>
                            <input x-model="editData.nama_beasiswa" type="text" name="nama" required
                                class="w-full mt-1 border rounded-lg px-3 py-2">
                        </div>

                        <div>
                            <label class="text-sm font-medium">Jenjang Beasiswa</label>
                            <textarea x-model="editData.jenjang_beasiswa" name="jenjang" rows="3" required
                                class="w-full mt-1 border rounded-lg px-3 py-2"></textarea>
                        </div>

                        <div>
                            <label class="text-sm font-medium">Mulai Pendaftaran</label>
                            <input x-model="editData.mulai_pendaftaran" type="date" name="mulai_pendaftaran" required
                                class="w-full mt-1 border rounded-lg px-3 py-2">
                        </div>

                        <div>
                            <label class="text-sm font-medium">Akhir Pendaftaran</label>
                            <input x-model="editData.akhir_pendaftaran" type="date" name="akhir_pendaftaran" required
                                class="w-full mt-1 border rounded-lg px-3 py-2">
                        </div>

                        <div>
                            <label class="text-sm font-medium">Syarat Beasiswa</label>
                            <textarea x-model="editData.syarat_beasiswa" name="syarat" rows="3" required
                                class="w-full mt-1 border rounded-lg px-3 py-2"></textarea>
                        </div>

                        <div>
                            <label class="text-sm font-medium">Benefit Beasiswa</label>
                            <textarea x-model="editData.benefit_beasiswa" name="benefit" rows="3" required
                                class="w-full mt-1 border rounded-lg px-3 py-2"></textarea>
                        </div>

                        <div>
                            <label class="text-sm font-medium">Pemberi Beasiswa</label>
                            <input x-model="editData.pemberi_beasiswa" type="text" name="pemberi" required
                                class="w-full mt-1 border rounded-lg px-3 py-2">
                        </div>

                        <div>
                            <label class="text-sm font-medium">Link Pendaftaran</label>
                            <input x-model="editData.link_pendaftaran" type="url" name="link" required
                                class="w-full mt-1 border rounded-lg px-3 py-2">
                        </div>

                        <input type="hidden" name="old_image" x-model="editData.image_url">

                        <!-- GAMBAR -->
                        <div x-data="{ preview: null }">
                            <label class="text-sm font-medium">Gambar Lomba (opsional)</label>
                            <input type="file" name="gambar" accept="image/*"
                                @change="preview = URL.createObjectURL($event.target.files[0])"
                                class="w-full mt-1 border rounded-lg px-3 py-2">

                            <template x-if="preview">
                                <img :src="preview" class="mt-3 w-full h-48 object-cover rounded-lg border">
                            </template>

                            <template x-if="!preview && editData.image_url">
                                <img :src="editData.image_url" class="mt-3 w-full h-48 object-cover rounded-lg border">
                            </template>
                        </div>

                        <div class="flex justify-end mt-4">
                            <button type="submit"
                                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 cursor-pointer">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- POPUP DELETE -->
            <div x-show="showDelete" x-cloak class="fixed inset-0 bg-black/40 flex items-center justify-center z-30">
                <div class="bg-white rounded-xl p-6 max-w-sm w-full">

                    <h2 class="text-lg text-gray-800 font-semibold mb-4">Hapus Beasiswa?</h2>

                    <p class="text-sm text-gray-600 mb-4">Data beasiswa dan gambar terkait akan dihapus permanen.</p>

                    <form action="{{ route('beasiswa.delete') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" x-model="deleteId">
                        <input type="hidden" name="image_url" x-model="editData.image_url">

                        <div class="flex justify-end gap-2 mt-4">
                            <button type="button" @click="showDelete = false"
                                class="px-4 py-2 border-gray-300 text-gray-800 cursor-pointer rounded-lg border">Batal</button>

                            <button type="submit"
                                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 cursor-pointer">
                                Hapus
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- Lomba Tab -->
    <div id="content-lomba" class="tab-content hidden">
        <div class="mb-4 flex justify-between items-center" x-data="{ showAdd: false }">
            <h2 class="text-xl font-semibold text-[#1CC8EE]">Lomba Management</h2>
            <button @click="showAdd = true"
                class="flex text-center gap-2 px-4 py-2 bg-[#1CC8EE] text-white rounded-lg hover:bg-[#1CC8EE]/80 cursor-pointer">
                <i class="hgi hgi-stroke hgi-add-01"></i>
                Add Lomba
            </button>

            <!-- POPUP TAMBAH LOMBA -->
            <div x-show="showAdd" x-cloak
                class="fixed inset-0 bg-black/40 flex items-center justify-center px-4 z-30 text-gray-800">
                <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6 max-h-120 overflow-y-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold ">Tambah Lomba</h2>
                        <button @click="showAdd = false" class="text-gray-600 cursor-pointer">
                            <i class="hgi hgi-stroke hgi-cancel-01 text-xl"></i>
                        </button>
                    </div>

                    <form action="{{ route('lomba.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-4">
                        @csrf

                        <!-- NAMA LOMBA -->
                        <div>
                            <label class="text-sm font-medium">Nama Lomba</label>
                            <input type="text" name="nama" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300">
                        </div>

                        <!-- TANGGAL PELAKSANAAN -->
                        <div>
                            <label class="text-sm font-medium">Tanggal Pelaksanaan</label>
                            <input type="date" name="tanggal_pelaksanaan" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300">
                        </div>

                        <!-- TANGGAL MULAI PENDAFTARAN -->
                        <div>
                            <label class="text-sm font-medium">Mulai Pendaftaran</label>
                            <input type="date" name="mulai_pendaftaran" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300">
                        </div>

                        <!-- TANGGAL AKHIR PENDAFTARAN -->
                        <div>
                            <label class="text-sm font-medium">Akhir Pendaftaran</label>
                            <input type="date" name="akhir_pendaftaran" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300">
                        </div>

                        <!-- LOKASI LOMBA -->
                        <div>
                            <label class="text-sm font-medium">Lokasi Lomba</label>
                            <input type="text" name="lokasi" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300">
                            </>
                        </div>

                        <!-- KATEGORI LOMBA -->
                        <div>
                            <label class="text-sm font-medium">Kategori Lomba</label>
                            <input type="text" name="kategori" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300">
                            </>
                        </div>

                        <!-- DESKRIPSI LOMBA -->
                        <div>
                            <label class="text-sm font-medium">Deskripsi Lomba</label>
                            <textarea name="deskripsi" rows="3" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"></textarea>
                        </div>

                        <!-- PENYELENGGARA BEASISWA -->
                        <div>
                            <label class="text-sm font-medium">Penyelenggara Beasiswa</label>
                            <input type="text" name="penyelenggara" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300"></input>
                        </div>

                        <!-- LINK PENDAFTARAN -->
                        <div>
                            <label class="text-sm font-medium">Link Pendaftaran</label>
                            <input type="url" name="link" required
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-300">
                        </div>

                        <!-- GAMBAR LOMBA -->
                        <div x-data="{ preview: null }">
                            <label class="text-sm font-medium">Gambar Lomba (opsional)</label>
                            <input type="file" name="gambar" accept="image/*" @change="
                                    const file = $event.target.files[0];
                                    preview = URL.createObjectURL(file);
                                "
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
        <div class="rounded-xl p-6 bg-gradient-to-r from-[#122E32] to-[#0B1A1C]" x-data="{showEdit: false, showDelete: false, deleteId: null, editData: {
                lomba_id: null,
                nama_lomba: '',
                tanggal_pelaksanaan: '',
                mulai_pendaftaran: '',
                akhir_pendaftaran: '',
                lokasi: '',
                kategori_lomba: '',
                deskripsi: '',
                penyelenggara: '',
                link_pendaftaran: '',
                image_url: ''
            } }" style="box-shadow:0 4px 10px rgba(0, 224, 255, 0.15);">
            <table class="min-w-full text-sm text-white">
                <thead>
                    <tr class="border-b border-[#1CC8EE]">
                        <th class="py-2 px-4 text-left">Nama Lomba</th>
                        <th class="py-2 px-4 text-center">Tanggal</th>
                        <th class="py-2 px-4 text-center">Penyelenggara</th>
                        <th class="py-2 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lombas ?? [] as $lomba)
                    <tr class="border-b border-gray-700">
                        <td class="py-2 px-4">{{ $lomba['nama_lomba'] ?? '-' }}</td>
                        <td class="py-2 px-4 text-center">
                            {{ isset($lomba['tanggal_pelaksanaan']) ? Carbon::parse($lomba['tanggal_pelaksanaan'])->format('d M Y') : '-' }}
                        </td>
                        <td class="py-2 px-4 text-center">{{ $lomba['penyelenggara'] ?? '-' }}</td>
                        <td class="py-2 px-4 text-center">
                            <div class="flex justify-center gap-2">

                                <!-- EDIT -->
                                <button @click="
                                    showEdit = true;
                                    editData.lomba_id = '{{ $lomba['lomba_id'] }}';
                                    editData.nama_lomba = '{{ $lomba['nama_lomba'] }}';
                                    editData.tanggal_pelaksanaan = '{{ $lomba['tanggal_pelaksanaan'] }}';
                                    editData.mulai_pendaftaran = '{{ $lomba['mulai_pendaftaran'] }}';
                                    editData.akhir_pendaftaran = '{{ $lomba['akhir_pendaftaran'] }}';
                                    editData.lokasi = '{{ $lomba['lokasi'] }}';
                                    editData.kategori_lomba = '{{ $lomba['kategori_lomba'] }}';
                                    editData.deskripsi = '{{ $lomba['deskripsi'] }}';
                                    editData.penyelenggara = '{{ $lomba['penyelenggara'] }}';
                                    editData.link_pendaftaran = '{{ $lomba['link_pendaftaran'] }}';
                                    editData.image_url = '{{ $lomba['image_url'] ?? '' }}';"
                                    class="px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs cursor-pointer">
                                    Edit
                                </button>

                                <!-- DELETE -->
                                <button @click="
                                    deleteId = '{{ $lomba['lomba_id'] }}';
                                    editData.image_url = '{{ $lomba['image_url'] ?? '' }}';
                                    showDelete = true;
                                                        "
                                    class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-xs cursor-pointer">
                                    Hapus
                                </button>

                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- POPUP EDIT SEMINAR -->
            <div x-show="showEdit" x-cloak
                class="fixed inset-0 bg-black/40 flex items-center justify-center px-4 z-30 text-gray-800">
                <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6 max-h-120 overflow-y-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold ">Edit Lomba</h2>
                        <button @click="showEdit = false" class="text-gray-600 cursor-pointer">
                            <i class="hgi hgi-stroke hgi-cancel-01 text-xl"></i>
                        </button>
                    </div>

                    <!-- POP UP EDIT -->
                    <form action="{{ route('lomba.update') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-4">
                        @csrf

                        <input type="hidden" name="id" :value="editData.lomba_id">

                        <div>
                            <label class="text-sm font-medium">Nama Lomba</label>
                            <input type="text" name="nama" x-model="editData.nama_lomba"
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2">
                        </div>

                        <div>
                            <label class="text-sm font-medium">Tanggal Pelaksanaan</label>
                            <input type="date" name="tanggal_pelaksanaan" x-model="editData.tanggal_pelaksanaan"
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2">
                        </div>

                        <div>
                            <label class="text-sm font-medium">Mulai Pendaftaran</label>
                            <input type="date" name="mulai_pendaftaran" x-model="editData.mulai_pendaftaran"
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2">
                        </div>

                        <div>
                            <label class="text-sm font-medium">Akhir Pendaftaran</label>
                            <input type="date" name="akhir_pendaftaran" x-model="editData.akhir_pendaftaran"
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2">
                        </div>

                        <div>
                            <label class="text-sm font-medium">Lokasi</label>
                            <input type="text" name="lokasi" x-model="editData.lokasi"
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2">
                        </div>

                        <div>
                            <label class="text-sm font-medium">Kategori</label>
                            <input type="text" name="kategori" x-model="editData.kategori_lomba"
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2">
                        </div>

                        <div>
                            <label class="text-sm font-medium">Penyelenggara</label>
                            <input type="text" name="penyelenggara" x-model="editData.penyelenggara"
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2">
                        </div>

                        <div>
                            <label class="text-sm font-medium">Deskripsi</label>
                            <textarea name="deskripsi" rows="3" x-model="editData.deskripsi"
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2"></textarea>
                        </div>

                        <div>
                            <label class="text-sm font-medium">Link Pendaftaran</label>
                            <input type="url" name="link" x-model="editData.link_pendaftaran"
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2">
                        </div>

                        <div>
                            <label class="text-sm font-medium">Gambar Seminar (opsional)</label>
                            <input type="file" name="gambar"
                                class="w-full mt-1 border border-gray-300 rounded-lg px-3 py-2">
                        </div>

                        <!-- untuk menyimpan gambar lama -->
                        <input type="hidden" name="old_image" :value="editData.image_url">

                        <div class="flex justify-end mt-4">
                            <button type="submit"
                                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 cursor-pointer">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>


                </div>
            </div>
            <!-- POPUP DELETE -->
            <div x-show="showDelete" x-cloak
                class="fixed inset-0 bg-black/40 flex items-center justify-center px-4 z-30">
                <div class="bg-white w-full max-w-sm rounded-xl shadow-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-800">Hapus Lomba?</h2>
                    <p class="text-sm text-gray-600 mt-2">
                        Lomba yang dihapus tidak dapat dikembalikan.
                    </p>

                    <div class="flex justify-end gap-3 mt-6">
                        <button @click="showDelete = false"
                            class="px-4 py-2 border-gray-300 text-gray-800 hover:bg-gray-200 rounded-lg border cursor-pointer">Batal</button>

                        <form action="{{ route('lomba.delete') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" :value="deleteId">
                            <input type="hidden" name="image_url" :value="editData.image_url">
                            <button type="submit"
                                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 cursor-pointer">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
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
</script>
@endsection