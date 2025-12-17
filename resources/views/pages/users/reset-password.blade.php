@extends('components._layouts.auth')
@section('content')
<div class="flex items-center md:h-auto min-h-screen overflow-hidden">
    <div
        class="bg-white w-full min-h-screen md:min-h-0 rounded-t-2xl md:rounded-2xl md:mx-auto mt-20 px-8 py-10 md:w-[450px] shadow-lg">
        <div class="text-gray-800 lg:text-center mb-6">
            <p class="font-bold text-2xl">Reset your password</p>
            <p class="text-sm text-gray-600 mt-2">Enter your new password below.</p>
        </div>
        <form class="space-y-6 w-full" id="newPasswordForm">
            @csrf
            <div class="space-y-6">
                <!-- Using x-ui components if available, otherwise fallback to manual input styling -->
                <div class="relative">
                    <input type="password" id="password" name="password"
                        class="peer p-4 block w-full border border-gray-300 rounded-lg sm:text-sm placeholder:text-transparent focus:border-[#27D5E8] focus:ring-[#27D5E8]"
                        placeholder="New password">
                    <label for="password"
                        class="absolute top-0 start-0 p-4 h-full sm:text-sm truncate pointer-events-none transition duration-100 origin-[0_0]
            peer-focus:scale-90 peer-focus:-translate-y-1.5 peer-focus:text-gray-500
            peer-not-placeholder-shown:scale-90 peer-not-placeholder-shown:-translate-y-1.5 peer-not-placeholder-shown:text-gray-500">New
                        password</label>
                </div>
                <div class="relative">
                    <input type="password" id="confirm" name="password_confirmation"
                        class="peer p-4 block w-full border border-gray-300 rounded-lg sm:text-sm placeholder:text-transparent focus:border-[#27D5E8] focus:ring-[#27D5E8]"
                        placeholder="Confirm password">
                    <label for="confirm"
                        class="absolute top-0 start-0 p-4 h-full sm:text-sm truncate pointer-events-none transition duration-100 origin-[0_0]
            peer-focus:scale-90 peer-focus:-translate-y-1.5 peer-focus:text-gray-500
            peer-not-placeholder-shown:scale-90 peer-not-placeholder-shown:-translate-y-1.5 peer-not-placeholder-shown:text-gray-500">Confirm
                        password</label>
                </div>
            </div>
            <button type="submit"
                class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-[#0D1517] text-white hover:bg-[#182427] focus:outline-hidden cursor-pointer disabled:opacity-50 transition-all">
                Update password
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
        <h2 class="font-bold text-lg text-gray-800">Success!</h2>
        <p class="text-gray-600 mt-2 text-sm" id="successMessage">Password has been updated.</p>
        <div class="mt-6">
            <button id="closeSuccessModal"
                class="px-4 py-2.5 bg-green-600 text-white hover:bg-green-700 rounded-lg w-full font-medium transition-colors">
                Login Now
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

        // Check if session is established from hash
        supabase.auth.onAuthStateChange(async (event, session) => {
            if (event == 'PASSWORD_RECOVERY') {
                // User is in recovery mode, this is good.
            }
        });

        const form = document.getElementById("newPasswordForm");
        const successModal = document.getElementById('successModal');
        const errorModal = document.getElementById('errorModal');
        const successMessage = document.getElementById('successMessage');
        const errorMessage = document.getElementById('errorMessage');

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
                window.location.href = "/";
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

                const password = document.getElementById("password").value;
                const confirm = document.getElementById("confirm").value;

                if (!password || !confirm) {
                    showError("Semua kolom harus diisi");
                    return;
                }

                if (password.length < 6) {
                    showError("Password minimal 6 karakter");
                    return;
                }

                if (password !== confirm) {
                    showError("Password konfirmasi tidak sama");
                    return;
                }

                const btn = form.querySelector('button[type="submit"]');
                const originalText = btn.innerText;
                btn.disabled = true;
                btn.innerText = "Updating...";

                try {
                    const { error } = await supabase.auth.updateUser({
                        password: password
                    });

                    if (error) throw error;
                    
                    showSuccess("Password berhasil diubah! Silakan login dengan password baru.");
                    form.reset();
                } catch (err) {
                    showError(err.message || "Gagal mengubah password.");
                } finally {
                    btn.disabled = false;
                    btn.innerText = originalText;
                }
            });
        }
    });
</script>
@endsection