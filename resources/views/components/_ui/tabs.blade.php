@php
    $active = $active
        ?? (request()->routeIs('events.seminar') ? 'seminar'
        : (request()->routeIs('events.beasiswa') ? 'beasiswa'
        : (request()->routeIs('events.lomba') ? 'lomba' : 'seminar')));
@endphp

<div class="rounded-2xl w-full max-w-3xl mx-auto 
    bg-gradient-to-r from-[#122E32] to-[#0B1A1C] shadow-[0_4px_10px_rgba(0,224,255,0.15)] px-6 py-3">

    <div class="flex items-center justify-between">
        <a href="{{ route('events.seminar') }}" 
           class="relative px-4 py-2 text-base font-medium transition"
           style="color:{{ $active==='seminar' ? '#FFFFFF' : '#B0C7CC' }};">
            Seminar
            @if ($active === 'seminar')
                <span class="absolute left-0 right-0 -bottom-1 h-1 rounded-full block bg-[#00E0FF]"></span>
            @endif
        </a>

        <a href="{{ route('events.beasiswa') }}" 
           class="relative px-4 py-2 text-base font-medium transition"
           style="color:{{ $active==='beasiswa' ? '#FFFFFF' : '#B0C7CC' }};">
            Beasiswa
            @if ($active === 'beasiswa')
                <span class="absolute left-0 right-0 -bottom-1 h-1 rounded-full block bg-[#00E0FF]"></span>
            @endif
        </a>

        <a href="{{ route('events.lomba') }}" 
           class="relative px-4 py-2 text-base font-medium transition"
           style="color:{{ $active==='lomba' ? '#FFFFFF' : '#B0C7CC' }};">
            Lomba
            @if ($active === 'lomba')
                <span class="absolute left-0 right-0 -bottom-1 h-1 rounded-full block bg-[#00E0FF]"></span>
            @endif
        </a>
    </div>
</div>
