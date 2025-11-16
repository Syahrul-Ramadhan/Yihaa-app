@extends('components._layouts.auth')
@section('content')
    <div class="flex items-center md:h-auto min-h-screen overflow-hidden">
        <div class="bg-white w-full min-h-screen md:min-h-0 rounded-t-2xl md:rounded-2xl md:mx-auto mt-20 px-6 py-10 md:w-1/3 md:mt-0">
            <div class="welcome-message text-gray-800 lg:text-center mb-5">
        <p class="font-bold text-2xl">Create your YIHAA account</p>
            </div>

      <!-- Form Register -->
        <div class="flex items-center justify-center ">
        <form id="yihaa-register-form" action="{{ route('register') }}" method="POST" class="space-y-6 mt-3 w-full max-w-sm">
          @csrf
            <div class="max-w-sm space-y-6">
            <div class="relative">
              <input type="text" id="register-name" name="name" class="peer p-4 block w-full border border-gray-300 rounded-lg sm:text-sm placeholder:text-transparent focus:border-[#27D5E8] focus:ring-[#27D5E8] focus:pt-6 focus:pb-2 not-placeholder-shown:pt-6 not-placeholder-shown:pb-2 autofill:pt-6 autofill:pb-2" placeholder="Your name">
              <label for="register-name" class="absolute top-0 start-0 p-4 h-full sm:text-sm truncate pointer-events-none transition duration-100 origin-[0_0]
                peer-focus:scale-90 peer-focus:-translate-y-1.5 peer-focus:text-gray-500
                peer-not-placeholder-shown:scale-90 peer-not-placeholder-shown:-translate-y-1.5 peer-not-placeholder-shown:text-gray-500 text-neutral-500">Name</label>
                        </div>
                        <x-_ui.emailInput/>
                        <x-_ui.passwordInput/>
                    </div>
        
                    <button type="submit" id="register-btn" class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-[#0D1517] text-white hover:bg-[#182427] focus:outline-hidden cursor-pointer disabled:opacity-50">
                    Register
                    </button>
                </form>
            </div>

            <div class="mt-6 text-center">
                <p class="text-base text-gray-800">Already have an account? <a href="/" class="font-bold text-base text-[#27D5E8] underline hover:text-[#198b97]">Login</a></p>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div id="successModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-80 text-center animate-fadeIn">
            <h2 class="text-xl font-bold mb-2">Berhasil!</h2>
            <p class="text-gray-700">{{ session('success') }}</p>

            <button id="okBtn" class="mt-4 w-full py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 cursor-pointer">
                Oke
            </button>
        </div>
    </div>
    @endif

    <script>
    document.addEventListener("DOMContentLoaded", () => {  
        const modal = document.getElementById("successModal");
        const okBtn = document.getElementById("okBtn");

        if(modal){
            // Auto redirect after 2.5s
            setTimeout(() => {
                window.location.href = "/";
            }, 2500);

            // Manual close button
            okBtn.addEventListener("click", () => {
                window.location.href = "/";
            });
        }
    });
    </script>

@endsection