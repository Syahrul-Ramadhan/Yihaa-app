@php
    $active = $active
        ?? (request()->routeIs('events.seminar') ? 'seminar'
        : (request()->routeIs('events.beasiswa') ? 'beasiswa'
        : (request()->routeIs('events.lomba') ? 'lomba' : 'seminar')));
@endphp

<div class="rounded-full px-4 py-2 w-full max-w-3xl" style="background-color:#0E2F3E; box-shadow:0 4px 10px rgba(0, 224, 255, 0.15);">
    <div class="flex items-center gap-8">
        <a href="{{ route('events.seminar') }}" class="relative px-2 py-2 text-base font-medium" style="color:{{ $active==='seminar' ? '#FFFFFF' : '#B0C7CC' }};">
            Seminar
            @if ($active === 'seminar')
                <span class="absolute left-0 right-0 -bottom-2 h-1 rounded-full block" style="background-color:#00E0FF;"></span>
            @endif
        </a>
        <a href="{{ route('events.beasiswa') }}" class="relative px-2 py-2 text-base font-medium" style="color:{{ $active==='beasiswa' ? '#FFFFFF' : '#B0C7CC' }};">
            Beasiswa
            @if ($active === 'beasiswa')
                <span class="absolute left-0 right-0 -bottom-2 h-1 rounded-full block" style="background-color:#00E0FF;"></span>
            @endif
        </a>
        <a href="{{ route('events.lomba') }}" class="relative px-2 py-2 text-base font-medium" style="color:{{ $active==='lomba' ? '#FFFFFF' : '#B0C7CC' }};">
            Lomba
            @if ($active === 'lomba')
                <span class="absolute left-0 right-0 -bottom-2 h-1 rounded-full block" style="background-color:#00E0FF;"></span>
            @endif
        </a>
    </div>
</div>

