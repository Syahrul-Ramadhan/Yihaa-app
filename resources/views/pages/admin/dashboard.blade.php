@extends('components._layouts.admin')
@section('content')
<div class="container mx-auto px-4 py-6 bg-gradient-to-l from-[#163F44] to-[#020C0D] min-h-screen">
    <h1 class="text-3xl font-bold mb-6 text-[#ffffff]">Dashboard</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Users -->
        <div class="rounded-xl p-6 flex items-center" style="bg-gradient-to-r from-[#122E32] to-[#0B1A1C]; box-shadow:0 4px 10px rgba(0, 224, 255, 0.15); color:#FFFFFF;">
            <div class="mr-4">
                <!-- Users Icon -->
                <svg width="36" height="36" fill="none" stroke="#1CC8EE" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 20h5v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M9 20H4v-2a4 4 0 0 1 3-3.87"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <div>
                <div class="text-[#ffffff] text-sm">Users</div>
                <div class="text-2xl font-bold text-white">{{ $usersCount ?? '0' }}</div>
            </div>
        </div>
        <!-- Materi -->
        <div class="rounded-xl p-6 flex items-center" style="bg-gradient-to-r from-[#122E32] to-[#0B1A1C]; box-shadow:0 4px 10px rgba(0, 224, 255, 0.15); color:#FFFFFF;">
            <div class="mr-4">
                <!-- Materi Icon -->
                <svg width="36" height="36" fill="none" stroke="#1CC8EE" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="5" width="18" height="14" rx="2"/>
                    <path d="M8 5V3h8v2"/>
                </svg>
            </div>
            <div>
                <div class="text-[#ffffff] text-sm">Materi</div>
                <div class="text-2xl font-bold text-white">{{ $materiCount ?? '0' }}</div>
            </div>
        </div>
        <!-- Events -->
        <div class="rounded-xl p-6 flex items-center" style="bg-gradient-to-r from-[#122E32] to-[#0B1A1C]; box-shadow:0 4px 10px rgba(0, 224, 255, 0.15); color:#FFFFFF;">
            <div class="mr-4">
                <!-- Events Icon -->
                <svg width="36" height="36" fill="none" stroke="#1CC8EE" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 8v4l3 3"/>
                </svg>
            </div>
            <div>
                <div class="text-[#ffffff] text-sm">Events</div>
                <div class="text-2xl font-bold text-white">{{ $eventsCount ?? '0' }}</div>
            </div>
        </div>
        <!-- Teams -->
        <div class="rounded-xl p-6 flex items-center" style="bg-gradient-to-r from-[#122E32] to-[#0B1A1C]; box-shadow:0 4px 10px rgba(0, 224, 255, 0.15); color:#FFFFFF;">
            <div class="mr-4">
                <!-- Teams Icon -->
                <svg width="36" height="36" fill="none" stroke="#1CC8EE" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="9" cy="7" r="4"/>
                    <circle cx="17" cy="7" r="4"/>
                    <path d="M5 21v-2a4 4 0 0 1 4-4h2"/>
                    <path d="M19 21v-2a4 4 0 0 0-4-4h-2"/>
                </svg>
            </div>
            <div>
                <div class="text-[#ffffff] text-sm">Teams</div>
                <div class="text-2xl font-bold text-white">{{ $teamsCount ?? '0' }}</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Tabel Materi -->
        <div class="rounded-xl p-6" style="bg-gradient-to-r from-[#122E32] to-[#0B1A1C]; box-shadow:0 4px 10px rgba(0, 224, 255, 0.15); color:#FFFFFF;">
            <h2 class="text-xl font-semibold mb-4 text-[#1CC8EE]">Materi Terbaru</h2>
            <table class="min-w-full text-sm text-white">
                <thead>
                    <tr class="border-b border-[#1CC8EE]">
                        <th class="py-2 px-4 text-left">Judul</th>
                        <th class="py-2 px-4 text-left">Deskripsi</th>
                        <th class="py-2 px-4 text-left">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($materiList ?? [] as $materials)
                    <tr class="border-b">
                        <td class="py-2 px-4">{{ $materials->title }}</td>
                        <td class="py-2 px-4">{{ $materials->description }}</td>
                        <td class="py-2 px-4">{{ $materials->created_at->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Tabel Events -->
        <div class="rounded-xl p-6" style="bg-gradient-to-r from-[#122E32] to-[#0B1A1C]; box-shadow:0 4px 10px rgba(0, 224, 255, 0.15); color:#FFFFFF;">
            <h2 class="text-xl font-semibold mb-4 text-[#1CC8EE]">Event Terbaru</h2>
            <table class="min-w-full text-sm text-white">
                <thead>
                    <tr class="border-b border-[#1CC8EE]">
                        <th class="py-2 px-4 text-left">Jenis Event</th>
                        <th class="py-2 px-4 text-left">Tanggal</th>
                        <th class="py-2 px-4 text-left">Nama Event</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($eventsList ?? [] as $event)
                    <tr class="border-b">
                        <td class="py-2 px-4">{{ $event->nama }}</td>
                        <td class="py-2 px-4">{{ $event->tanggal->format('d M Y') }}</td>
                        <td class="py-2 px-4">{{ $event->nama_event }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-xl p-6 mb-8" style="bg-gradient-to-r from-[#122E32] to-[#0B1A1C]; box-shadow:0 4px 10px rgba(0, 224, 255, 0.15); color:#FFFFFF;">
        <h2 class="text-xl font-semibold mb-4 text-[#1CC8EE]">Daftar Tim</h2>
        <table class="min-w-full text-sm text-white">
            <thead>
                <tr class="border-b border-[#1CC8EE]">
                    <th class="py-2 px-4 text-left">Nama Tim</th>
                    <th class="py-2 px-4 text-left">Anggota</th>
                </tr>
            </thead>
            <tbody>
                @foreach($teamsList ?? [] as $teams)
                <tr class="border-b">
                    <td class="py-2 px-4">{{ $team['team_name'] }}</td>
                    <td class="py-2 px-4">{{ $team['team_desc'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection