@php
    $active = $active
        ?? (request()->routeIs('seminar') ? 'seminar'
        : (request()->routeIs('beasiswa') ? 'beasiswa'
        : (request()->routeIs('lomba') ? 'lomba' : 'seminar')));
@endphp

<div class="flex items-center justify-between px-6">
    <a href="{{ route('seminar') }}"
       class="relative px-4 py-2 text-base font-medium transition {{ $active === 'seminar' ? 'text-white' : 'text-[#B0C7CC]' }}">
        Seminar
        @if ($active === 'seminar')
            <span class="absolute left-0 right-0 -bottom-1 h-1 rounded-full block bg-[#00E0FF]"></span>
        @endif
    </a>

    <a href="{{ route('beasiswa') }}"
       class="relative px-4 py-2 text-base font-medium transition {{ $active === 'beasiswa' ? 'text-white' : 'text-[#B0C7CC]' }}">
        Beasiswa
        @if ($active === 'beasiswa')
            <span class="absolute left-0 right-0 -bottom-1 h-1 rounded-full block bg-[#00E0FF]"></span>
        @endif
    </a>

    <a href="{{ route('lomba') }}"
       class="relative px-4 py-2 text-base font-medium transition {{ $active === 'lomba' ? 'text-white' : 'text-[#B0C7CC]' }}">
        Lomba
        @if ($active === 'lomba')
            <span class="absolute left-0 right-0 -bottom-1 h-1 rounded-full block bg-[#00E0FF]"></span>
        @endif
    </a>
</div>
