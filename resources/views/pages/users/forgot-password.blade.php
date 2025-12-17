@extends('components._layouts.auth')
@section('content')
@extends('components._layouts.auth')
@section('content')
<div class="flex items-center md:h-auto min-h-screen overflow-hidden">
    <div
        class="bg-white w-full min-h-screen md:min-h-0 rounded-t-2xl md:rounded-2xl md:mx-auto mt-20 px-8 py-10 md:w-[450px] shadow-lg">
        <div class="text-gray-800 lg:text-center mb-6">
            <p class="font-bold text-2xl">Forgot your password?</p>
            <p class="text-sm text-gray-600 mt-2">Enter your email and we'll send you a reset link.</p>
        </div>
        <form class="space-y-6 w-full" id="resetForm">
            @csrf
            <div class="space-y-6 mb-6">
                <x-_ui.emailInput />
            </div>
            <button type="submit"
                class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-[#0D1517] text-white hover:bg-[#182427] focus:outline-hidden cursor-pointer disabled:opacity-50 transition-all">
                Send reset link
            </button>
            <p class="text-center text-sm text-gray-600 mt-4"><a href="/" class="text-[#27D5E8] underline hover:text-[#198b97]">Back to
                    login</a></p>
        </form>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="hidden fixed inset-0 bg-black/60 items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 text-center w-80 shadow-xl transform transition-all scale-100">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h2 class="font-bold text-lg text-gray-800">Check Your Email</h2>
        <p class="text-gray-600 mt-2 text-sm" id="successMessage">Link reset password has been sent.</p>
        <div class="mt-6">
            <button id="closeSuccessModal"
                class="px-4 py-2.5 bg-gray-900 text-white hover:bg-gray-800 rounded-lg w-full font-medium transition-colors">
                Okay
            </button>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="hidden fixed inset-0 bg-black/60 items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 text-center w-80 shadow-xl">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </div>
        <h2 class="font-bold text-lg text-gray-800">Failed</h2>
        <p class="text-gray-600 mt-2 text-sm" id="errorMessage">Something went wrong.</p>
        <div class="mt-6">
            <button id="closeErrorModal"
                class="px-4 py-2.5 bg-gray-900 text-white hover:bg-gray-800 rounded-lg w-full font-medium transition-colors">
                Try Again
            </button>
        </div>
    </div>
</div>

<script type="module">
    import {
        createClient
    } from 'https://esm.sh/@supabase/supabase-js';

    document.addEventListener("DOMContentLoaded", () => {
        const supabase = createClient(
            "{{ env('SUPABASE_URL') }}",
            "{{ env('SUPABASE_ANON_KEY') }}"
        );

        const form = document.getElementById("resetForm");
        // Check for email input by ID first, if not found try name attribute
        let emailInput = document.getElementById("email");
        if (!emailInput) {
             emailInput = document.querySelector('input[name="email"]');
        }

        const successModal = document.getElementById('successModal');
        const errorModal = document.getElementById('errorModal');
        const successMessage = document.getElementById('successMessage');
        const errorMessage = document.getElementById('errorMessage');

        // Modal Handlers
        function showSuccess(msg) {
            successMessage.textContent = msg;
            successModal.classList.remove('hidden');
            successModal.classList.add('flex');
        }

        function showError(msg) {
            errorMessage.textContent = msg;
            errorModal.classList.remove('hidden');
            errorModal.classList.add('flex');
        }

        if (document.getElementById('closeSuccessModal')) {
            document.getElementById('closeSuccessModal').addEventListener('click', () => {
                successModal.classList.add('hidden');
                successModal.classList.remove('flex');
                form.reset();
            });
        }

        if (document.getElementById('closeErrorModal')) {
            document.getElementById('closeErrorModal').addEventListener('click', () => {
                errorModal.classList.add('hidden');
                errorModal.classList.remove('flex');
            });
        }

        if (form) {
            form.addEventListener("submit", async (e) => {
                e.preventDefault();
                
                // Re-query input in case x-ui component renders it differently
                if (!emailInput) emailInput = document.querySelector('input[name="email"]');

                const email = emailInput ? emailInput.value.trim() : '';

                if (!email) {
                    showError("Email wajib diisi");
                    return;
                }

                const btn = form.querySelector('button[type="submit"]');
                const originalText = btn.innerText;
                btn.disabled = true;
                btn.innerText = "Sending...";

                try {
                    const { error } = await supabase.auth.resetPasswordForEmail(email, {
                        redirectTo: "{{ url('/reset-password') }}"
                    });

                    if (error) throw error;
                    
                    showSuccess("Link reset password telah dikirim ke email Anda.");
                } catch (err) {
                    showError(err.message || "Gagal mengirim link reset.");
                } finally {
                    btn.disabled = false;
                    btn.innerText = originalText;
                }
            });
        }
    });
</script>

@endsection