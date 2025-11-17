@extends('components._layouts.auth')
@section('content')
    <div class="flex items-center md:h-auto min-h-screen overflow-hidden">
        <div class="bg-white w-full min-h-screen md:min-h-0 rounded-t-2xl md:rounded-2xl md:mx-auto mt-20 px-6 py-10 md:w-1/3 md:mt-0">
            <div class="welcome-message text-gray-800 lg:text-center mb-5">
                <p class="font-bold text-2xl sm:text">Sign in to YIHAA</p>
            </div>

            <!-- Form Login -->
            <div class="flex items-center justify-center">
                <form id="yihaa-login-form" class="space-y-6 mt-3 w-full max-w-sm" action="{{ route('login.process') }}" method="POST">
                    @csrf
                    <div class="max-w-sm space-y-6">
                        <x-_ui.emailInput/>
                        <x-_ui.passwordInput/>
                    </div>
        
                    <div class="flex justify-end items-center">
                        <a href="/forgot-password" class="text-base text-[#27D5E8] underline hover:text-[#198b97]">Forgot password?</a>
                    </div>
        
                    <button type="submit" id="sign-in-btn" class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-[#0D1517] text-white hover:bg-[#182427] cursor-pointer">
                    Sign In
                    </button>
                </form>
            </div>

            <div class="mt-6 text-center">
                <p class="text-base text-gray-800">Don't have an account? <a href="/register" class="font-bold text-base text-[#27D5E8] underline hover:text-[#198b97]">Sign up</a></p>
            </div>
        </div>
    </div>

    @if(session('login_success'))
    <div id="successModal"
        class="fixed inset-0 bg-black/60 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 text-center w-72">
            <h2 class="font-bold text-lg text-blue-600">Success!</h2>
            <p class="text-gray-700 mt-2">{{ session('login_success') }}</p>

            <div class="mt-5">
                <button id="closeModalBtn" 
                    class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-lg w-full cursor-pointer">
                    Oke
                </button>
            </div>
        </div>
    </div>

    @elseif(session('logout_success'))
    <div id="logoutModal"
        class="fixed inset-0 bg-black/60 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 text-center w-72">
            <h2 class="font-bold text-lg text-green-600">Logout Berhasil!</h2>
            <p class="text-gray-700 mt-2">{{ session('logout_success') }}</p>

            <div class="mt-5">
                <button id="closeLogoutModalBtn" 
                    class="px-4 py-2 bg-green-600 text-white hover:bg-green-700 rounded-lg w-full cursor-pointer">
                    Oke
                </button>
            </div>
        </div>
    </div>

    @elseif(session('error'))
    <div id="ErrorModal"
        class="fixed inset-0 bg-black/60 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 text-center w-72">
            <h2 class="font-bold text-lg text-red-600">Gagal Login!</h2>
            <p class="text-gray-700 mt-2">{{ session('error') }}</p>

            <div class="mt-5">
                <button id="closeErrorModalBtn" 
                    class="px-4 py-2 bg-red-600 text-white hover:bg-red-700 rounded-lg w-full cursor-pointer">
                    Oke
                </button>
            </div>
        </div>
    </div>
    @endif

    <script>
    // Jika Login Berhasil - redirect ke home
    const successBtn = document.getElementById('closeModalBtn');
    if (successBtn) {
        successBtn.addEventListener('click', function () {
            window.location.href = "/home";
        });
    }

    // Jika Logout Berhasil - just close modal, stay on login page
    const logoutBtn = document.getElementById('closeLogoutModalBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function () {
            document.getElementById('logoutModal').style.display = 'none';
        });
    }

    // Jika modal error, reload halaman saat klik Oke
    const errorBtn = document.getElementById('closeErrorModalBtn');
    if (errorBtn) {
        errorBtn.addEventListener('click', function () {
            window.location.reload(); // ulang halaman login
        });
    }

    </script>

@endsection