@php
    use Illuminate\Support\Str;

    $image = $image ?? Vite::asset('resources/images/logo.png');
    $title = $title ?? 'Event Title';
    $details = $details ?? [
        ['label' => 'Tanggal', 'value' => '4 Mei 2023'],
        ['label' => 'Lokasi', 'value' => 'Zoom Meet'],
        ['label' => 'Pembicara', 'value' => 'Kak Teeya'],
        ['label' => 'Deskripsi', 'value' => 'Lorem ipsum bdhduwefufbufw bbkwhef...'],
        ['label' => 'Link Daftar', 'value' => 'bit.ly/example'],
    ];
    $button = $button ?? ['text' => 'Apply', 'href' => '#'];
@endphp

<div class="rounded-2xl p-6 flex gap-6 text-white 
    bg-gradient-to-r from-[#122E32] to-[#0B1A1C] 
    shadow-[0_4px_10px_rgba(0,224,255,0.15)]">

    <div class="shrink-0">
        <img src="{{ $image }}" alt="poster" class="w-44 h-56 object-cover rounded-xl" />
    </div>

    <div class="flex-1">
        <h3 class="text-xl font-semibold mb-4">{{ $title }}</h3>

        <div class="grid grid-cols-12 gap-y-2 text-sm">
            @foreach ($details as $row)
                @php
                    $label = $row['label'] ?? '';
                    $value = (string) ($row['value'] ?? '');
                @endphp

                <div class="col-span-3 text-white">{{ $label }}</div>

                {{-- If the value is long (over 100 chars) we show a truncated preview and a Read more toggle.
                     We use Alpine.js per-row so each toggle is independent and beginner-friendly. --}}
                @if(Str::length($value) > 100)
                    <div class="col-span-9 text-[#B0C7CC]" x-data="{ open: false }">
                        :
                        <span x-show="!open">{{ Str::limit($value, 100) }}</span>
                        <span x-show="open" x-cloak>{{ $value }}</span>
                        <button @click="open = !open" class="ml-2 text-sm text-[#00E0FF]">
                            <span x-text="open ? 'Read less' : 'Read more'"></span>
                        </button>
                    </div>
                @else
                    <div class="col-span-9 text-[#B0C7CC]">: {{ $value }}</div>
                @endif
            @endforeach
        </div>

        <div class="mt-5">
            <a href="{{ $button['href'] }}" 
               class="inline-flex items-center justify-center rounded-full px-6 py-2 font-medium text-[#0B1A1C] bg-[#00E0FF] hover:bg-[#00bcd4] transition">
                {{ $button['text'] }}
            </a>
        </div>
    </div>
</div>
