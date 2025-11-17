<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

    
</head>

<body class="bg-gradient-to-l from-[#163F44] to-[#020C0D]">
    
    @yield('content')

    <!-- Global Modal -->
    <div id="globalModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300 z-50">
    <div class="bg-white rounded-2xl p-6 w-80 text-center shadow-xl scale-90 transition-all duration-300">
        <h3 id="modalTitle" class="font-bold text-lg mb-2"></h3>
        <p id="modalMessage" class="text-sm text-gray-600 mb-5"></p>

        <button id="modalBtn" 
        class="bg-[#0D1517] text-white w-full py-2 rounded-lg hover:bg-[#182427] transition">
        OK
        </button>
    </div>
    </div>

    <script>
        function showModal(title, message, callback = null) {
        const modal = document.getElementById("globalModal");
        const modalTitle = document.getElementById("modalTitle");
        const modalMessage = document.getElementById("modalMessage");
        const modalBtn = document.getElementById("modalBtn");

        modalTitle.textContent = title;
        modalMessage.textContent = message;

        modal.classList.remove("opacity-0", "pointer-events-none");
        modal.classList.add("opacity-100");

        modalBtn.onclick = () => {
            modal.classList.add("opacity-0", "pointer-events-none");
            if (callback) callback();
        };
        }
    </script>

</body>

</html>
