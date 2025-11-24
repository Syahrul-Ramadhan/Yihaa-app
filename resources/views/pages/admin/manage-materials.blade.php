@extends('components._layouts.admin')
@section('content')
@php
    use Carbon\Carbon;
@endphp
<div class="container mx-auto px-4 py-6 bg-gradient-to-l from-[#163F44] to-[#020C0D] min-h-screen">
    <h1 class="text-3xl font-bold mb-6 text-[#ffffff]">Manage Materials</h1>

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
        <button onclick="switchTab('pending')" id="tab-pending" class="tab-btn px-6 py-3 font-semibold text-[#1CC8EE] border-b-2 border-[#1CC8EE] relative">
            Pending Requests
            @if(count($pending ?? []) > 0)
                <span class="absolute top-0 right-0 bg-red-500 text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">{{ count($pending) }}</span>
            @endif
        </button>
        <button onclick="switchTab('approved')" id="tab-approved" class="tab-btn px-6 py-3 font-semibold text-gray-400 hover:text-white">Approved</button>
        <button onclick="switchTab('rejected')" id="tab-rejected" class="tab-btn px-6 py-3 font-semibold text-gray-400 hover:text-white">Rejected</button>
        <button onclick="switchTab('all')" id="tab-all" class="tab-btn px-6 py-3 font-semibold text-gray-400 hover:text-white">All Materials</button>
    </div>

    <div class="mb-4 flex justify-end">
        <button onclick="openModal('create')" class="px-4 py-2 bg-[#1CC8EE] text-white rounded-lg hover:bg-[#1CC8EE]/80">+ Add Material</button>
    </div>

    <!-- Pending Requests Tab -->
    <div id="content-pending" class="tab-content">
        <h2 class="text-xl font-semibold mb-4 text-[#1CC8EE]">Pending Material Requests</h2>
        <div class="rounded-xl p-6" style="background: linear-gradient(to right, #122E32, #0B1A1C); box-shadow: 0 4px 10px rgba(0, 224, 255, 0.15);">
            @if(count($pending ?? []) > 0)
                <div class="space-y-4">
                    @foreach($pending as $material)
                    <div class="border-b border-gray-700 pb-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-white mb-2">{{ $material['tittle'] ?? '-' }}</h3>
                                <p class="text-gray-300 mb-2">{{ Str::limit($material['description'] ?? '-', 150) }}</p>
                                <div class="flex gap-4 text-sm text-gray-400">
                                    <span>Uploaded by: {{ $material['users']['name'] ?? 'Unknown' }}</span>
                                    <span>•</span>
                                    <span>{{ isset($material['created_at']) ? Carbon::parse($material['created_at'])->format('d M Y H:i') : '-' }}</span>
                                </div>
                                @if($material['file_url'] ?? null)
                                <a href="{{ $material['file_url'] }}" target="_blank" class="text-blue-400 hover:text-blue-300 text-sm mt-2 inline-block">View File →</a>
                                @endif
                            </div>
                            <div class="flex gap-2 ml-4">
                                <form action="{{ route('admin.materials.approve', $material['material_id']) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">Approve</button>
                                </form>
                                <form action="{{ route('admin.materials.reject', $material['material_id']) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">Reject</button>
                                </form>
                                <!-- <button onclick="openModal('edit', {{ json_encode($material) }})" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Edit</button> -->
                                <form action="{{ route('admin.materials.delete', $material['material_id']) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 text-center py-8">No pending material requests</p>
            @endif
        </div>
    </div>

    <!-- Approved Tab -->
    <div id="content-approved" class="tab-content hidden">
        <h2 class="text-xl font-semibold mb-4 text-[#1CC8EE]">Approved Materials</h2>
        <div class="rounded-xl p-6" style="background: linear-gradient(to right, #122E32, #0B1A1C); box-shadow: 0 4px 10px rgba(0, 224, 255, 0.15);">
            @if(count($approved ?? []) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($approved as $material)
                    <div class="border border-gray-700 rounded-lg p-4 hover:border-[#1CC8EE] transition">
                        <h3 class="text-lg font-semibold text-white mb-2">{{ $material['tittle'] ?? '-' }}</h3>
                        <p class="text-gray-300 text-sm mb-3">{{ Str::limit($material['description'] ?? '-', 100) }}</p>
                        <div class="text-xs text-gray-400 mb-3">
                            <div>By: {{ $material['users']['name'] ?? 'Unknown' }}</div>
                            <div>{{ isset($material['created_at']) ? Carbon::parse($material['created_at'])->format('d M Y') : '-' }}</div>
                        </div>
                        <div class="flex gap-2 mt-4">
                            <!-- <button onclick="openModal('edit', {{ json_encode($material) }})" class="flex-1 px-3 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm">Edit</button> -->
                            <form action="{{ route('admin.materials.delete', $material['material_id']) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 text-center py-8">No approved materials</p>
            @endif
        </div>
    </div>

    <!-- Rejected Tab -->
    <div id="content-rejected" class="tab-content hidden">
        <h2 class="text-xl font-semibold mb-4 text-[#1CC8EE]">Rejected Materials</h2>
        <div class="rounded-xl p-6" style="background: linear-gradient(to right, #122E32, #0B1A1C); box-shadow: 0 4px 10px rgba(0, 224, 255, 0.15);">
            @if(count($rejected ?? []) > 0)
                <div class="space-y-4">
                    @foreach($rejected as $material)
                    <div class="border-b border-gray-700 pb-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-white mb-2">{{ $material['tittle'] ?? '-' }}</h3>
                                <p class="text-gray-300 mb-2">{{ Str::limit($material['description'] ?? '-', 150) }}</p>
                                <div class="flex gap-4 text-sm text-gray-400">
                                    <span>Uploaded by: {{ $material['users']['name'] ?? 'Unknown' }}</span>
                                    <span>•</span>
                                    <span>{{ isset($material['created_at']) ? Carbon::parse($material['created_at'])->format('d M Y H:i') : '-' }}</span>
                                </div>
                            </div>
                            <div class="flex gap-2 ml-4">
                                <!-- <button onclick="openModal('edit', {{ json_encode($material) }})" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Edit</button> -->
                                <form action="{{ route('admin.materials.delete', $material['material_id']) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 text-center py-8">No rejected materials</p>
            @endif
        </div>
    </div>

    <!-- All Materials Tab -->
    <div id="content-all" class="tab-content hidden">
        <h2 class="text-xl font-semibold mb-4 text-[#1CC8EE]">All Materials</h2>
        <div class="rounded-xl p-6" style="background: linear-gradient(to right, #122E32, #0B1A1C); box-shadow: 0 4px 10px rgba(0, 224, 255, 0.15);">
            <table class="min-w-full text-sm text-white">
                <thead>
                    <tr class="border-b border-[#1CC8EE]">
                        <th class="py-2 px-4 text-left">Title</th>
                        <th class="py-2 px-4 text-left">Uploaded By</th>
                        <th class="py-2 px-4 text-left">Status</th>
                        <th class="py-2 px-4 text-left">Date</th>
                        <th class="py-2 px-4 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($materials ?? [] as $material)
                    <tr class="border-b border-gray-700">
                        <td class="py-2 px-4">{{ $material['tittle'] ?? '-' }}</td>
                        <td class="py-2 px-4">{{ $material['users']['name'] ?? 'Unknown' }}</td>
                        <td class="py-2 px-4">
                            @php
                                $status = $material['status'] ?? 'pending';
                                $statusClass = $status === 'approved' ? 'bg-green-500/20 text-green-400' : ($status === 'rejected' ? 'bg-red-500/20 text-red-400' : 'bg-yellow-500/20 text-yellow-400');
                            @endphp
                            <span class="px-2 py-1 rounded {{ $statusClass }} text-xs">{{ ucfirst($status) }}</span>
                        </td>
                        <td class="py-2 px-4">{{ isset($material['created_at']) ? Carbon::parse($material['created_at'])->format('d M Y') : '-' }}</td>
                        <td class="py-2 px-4">
                            <div class="flex gap-2">
                                <!-- <button onclick="openModal('edit', {{ json_encode($material) }})" class="text-blue-400 hover:text-blue-300 text-sm">Edit</button> -->
                                <form action="{{ route('admin.materials.delete', $material['material_id']) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 text-sm">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="materialModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-[#122E32] rounded-xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto" style="box-shadow:0 4px 10px rgba(0, 224, 255, 0.15);">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-xl font-semibold text-[#1CC8EE]"></h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-white">✕</button>
        </div>
        <form id="materialForm" method="POST">
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
let currentMode = 'create';
let currentId = null;

function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('text-[#1CC8EE]', 'border-[#1CC8EE]', 'border-b-2');
        el.classList.add('text-gray-400');
    });
    
    document.getElementById(`content-${tab}`).classList.remove('hidden');
    const btn = document.getElementById(`tab-${tab}`);
    btn.classList.remove('text-gray-400');
    btn.classList.add('text-[#1CC8EE]', 'border-b-2', 'border-[#1CC8EE]');
}

function openModal(mode, data = null) {
    currentMode = mode;
    currentId = data ? data.material_id : null;
    
    const modal = document.getElementById('materialModal');
    const title = document.getElementById('modalTitle');
    const form = document.getElementById('materialForm');
    const content = document.getElementById('formContent');
    
    title.textContent = `${mode === 'create' ? 'Create' : 'Edit'} Material`;
    
    let action = '';
    if (mode === 'create') {
        action = '{{ route("admin.materials.store") }}';
        form.innerHTML = '@csrf';
    } else {
        action = `{{ url('/admin/materials') }}/${currentId}`;
        form.innerHTML = '@csrf @method("PUT")';
    }
    form.action = action;
    
    content.innerHTML = `
        <div>
            <label class="block text-white mb-2">Title *</label>
            <input type="text" name="tittle" value="${data?.tittle || ''}" required class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">
        </div>
        <div>
            <label class="block text-white mb-2">Description</label>
            <textarea name="description" rows="4" class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">${data?.description || ''}</textarea>
        </div>
        <div>
            <label class="block text-white mb-2">File URL</label>
            <input type="url" name="file_url" value="${data?.file_url || ''}" class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">
        </div>
        <div>
            <label class="block text-white mb-2">Thumbnail URL</label>
            <input type="url" name="thumbnail_url" value="${data?.thumbnail_url || ''}" class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">
        </div>
        ${mode === 'edit' ? `
        <div>
            <label class="block text-white mb-2">Status</label>
            <select name="status" class="w-full px-4 py-2 bg-[#0B1A1C] border border-gray-600 rounded-lg text-white focus:border-[#1CC8EE]">
                <option value="pending" ${(data?.status || 'pending') === 'pending' ? 'selected' : ''}>Pending</option>
                <option value="approved" ${(data?.status || 'pending') === 'approved' ? 'selected' : ''}>Approved</option>
                <option value="rejected" ${(data?.status || 'pending') === 'rejected' ? 'selected' : ''}>Rejected</option>
            </select>
        </div>
        ` : ''}
    `;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal() {
    document.getElementById('materialModal').classList.add('hidden');
    document.getElementById('materialModal').classList.remove('flex');
    document.getElementById('materialForm').reset();
}
</script>
@endsection
