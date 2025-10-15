@php
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

<div class="rounded-2xl p-6 flex gap-6 text-white" style="background-color:#0E2F3E; box-shadow:0 4px 10px rgba(0, 224, 255, 0.15);">
    <div class="shrink-0">
        <img src="{{ $image }}" alt="poster" class="w-44 h-56 object-cover rounded-xl" />
    </div>
    <div class="flex-1">
        <h3 class="text-xl font-semibold mb-4">{{ $title }}</h3>
        <div class="grid grid-cols-12 gap-y-2 text-sm">
            @foreach ($details as $row)
                <div class="col-span-3" style="color:#FFFFFF">{{ $row['label'] }}</div>
                <div class="col-span-9" style="color:#B0C7CC">: {{ $row['value'] }}</div>
            @endforeach
        </div>
        <div class="mt-5">
            <a href="{{ $button['href'] }}" class="inline-flex items-center justify-center rounded-full text-white px-6 py-2 font-medium" style="background-color:#00E0FF;">
                {{ $button['text'] }}
            </a>
        </div>
    </div>
</div>

