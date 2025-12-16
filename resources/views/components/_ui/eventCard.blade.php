@php
use Illuminate\Support\Str;

$image = $image ?? Vite::asset('resources/images/logo.png');
$title = $title ?? 'Event Title';
$details = $details ?? [];
$button = $button ?? ['text' => 'Apply', 'href' => '#'];
@endphp

<div class="rounded-2xl p-6 flex gap-6 text-white 
           bg-gradient-to-r from-[#122E32] to-[#0B1A1C]
           shadow-[0_4px_10px_rgba(0,224,255,0.15)]">

    {{-- Poster --}}
    <div class="shrink-0">
        <img src="{{ $image }}" alt="poster" class="w-44 h-56 object-cover rounded-xl" />
    </div>

    {{-- Content --}}
    <div class="flex-1 min-w-0">
        {{-- Title --}}
        <h3 class="text-xl font-semibold mb-4 truncate" title="{{ $title }}">
            {{ $title }}
        </h3>

        {{-- Detail List --}}
        <div class="grid grid-cols-12 gap-y-2 text-sm">
            @foreach ($details as $row)
            @php
            $label = $row['label'] ?? '';
            $value = trim((string) ($row['value'] ?? '-'));
            $isLink = Str::startsWith($value, ['http://', 'https://']);
            @endphp

            {{-- Label --}}
            <div class="col-span-3 text-white">
                {{ $label }}
            </div>

            <div class="col-span-1">:</div>

            {{-- Value --}}
            <div class="col-span-8 text-[#B0C7CC] min-w-0">
                {{-- LINK --}}
                @if($isLink)
                <a href="{{ $value }}" target="_blank" class="text-[#00E0FF] line-clamp-1 hover:underline"
                    title="{{ $value }}">
                    {{ $value }}
                </a>

                {{-- TEKS PANJANG --}}
                @elseif(Str::length($value) > 120)
                <span x-data="{ open: false }" class="inline">
                    <span x-show="!open" class="line-clamp-2 break-words" title="{{ $value }}">
                        {{ $value }}
                    </span>

                    <span x-show="open" x-cloak class="break-words">
                        {{ $value }}
                    </span>

                    <button @click="open = !open" class=" text-xs text-[#00E0FF] hover:underline cursor-pointer">
                        <span x-text="open ? 'Tutup' : 'Selengkapnya'"></span>
                    </button>
                </span>

                {{-- TEKS PENDEK --}}
                @else
                <span title="{{ $value }}">
                    {{ $value }}
                </span>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Action Button --}}
        <div class="mt-5">
            <a href="{{ $button['href'] }}" target="_blank" class="inline-flex items-center justify-center
                       rounded-full px-6 py-2 font-medium
                       text-[#0B1A1C] bg-[#00E0FF]
                       hover:bg-[#00bcd4] transition">
                {{ $button['text'] }}
            </a>
        </div>
    </div>
</div>