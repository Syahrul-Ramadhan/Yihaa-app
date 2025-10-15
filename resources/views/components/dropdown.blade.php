@props(['align' => 'right', 'width' => '48'])

@php
    $alignmentClasses = $align === 'left' ? 'origin-top-left start-0' : 'origin-top-right end-0';
    $widthClass = 'w-' . $width;
@endphp

<div x-data="{ open: false }" class="relative">
    <div @click="open = !open">
        {{ $trigger }}
    </div>

    <div x-cloak x-show="open" @click.outside="open = false" class="absolute z-50 mt-2 rounded-md shadow-lg {{ $alignmentClasses }}">
        <div class="rounded-md ring-1 ring-black ring-opacity-5 bg-white dark:bg-gray-800 {{ $widthClass }}">
            {{ $content }}
        </div>
    </div>
</div>

