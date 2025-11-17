<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.hugeicons.com/font/hgi-stroke-rounded.css" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <script src="//unpkg.com/alpinejs" defer></script>
    <style>[x-cloak] { display: none}</style>
</head>

<body class="min-h-screen flex text-white bg-gradient-to-l from-[#163F44] to-[#020C0D]" x-data="{ open: true }">
    <x-_ui.sidebar />
    
    <main class="flex-1 p-6 overflow-y-auto transition-all duration-300" :class="open ? 'ml-64' : 'ml-20'">
        @yield('content')
    </main>
    

</body>

</html>
