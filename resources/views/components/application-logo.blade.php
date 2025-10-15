@props(['class' => ''])

<img src="{{ Vite::asset('resources/images/logo2.png') }}" alt="Logo" {{ $attributes->merge(['class' => trim('h-9 w-auto '.$class)]) }}>

