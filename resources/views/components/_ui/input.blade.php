@props([
    'type' => 'text',
    'name' => null,
    'id' => null,
    'placeholder' => '',
    'for' => '',
])

<div class="max-w-sm space-y-3">
  <label for="{{ $id }}" class="block text-sm font-medium mb-2 text-gray-800">{{ $for }}</label>
  <input type="{{ $type }}" name="{{ $name }}" placeholder="{{ $placeholder }}" id="{{ $id }}"
        class = "py-2.5 sm:py-3 px-4 block w-full border border-gray-400 rounded-lg sm:text-sm focus:border-red focus:ring-[#27D5E8] focus:ring-1 focus:outline-none disabled:opacity-50 disabled:pointer-events-none mt-2">
</div>