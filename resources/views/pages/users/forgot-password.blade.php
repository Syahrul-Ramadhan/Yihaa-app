@extends('components._layouts.auth')
@section('content')
<div class="flex items-center md:h-auto min-h-screen overflow-hidden">
    <div
        class="bg-white w-full min-h-screen md:min-h-0 rounded-t-2xl md:rounded-2xl md:mx-auto mt-12 px-6 py-10 md:w-1/3 md:mt-0">
        <div class="text-gray-800 lg:text-center mb-6">
            <p class="font-bold text-2xl">Forgot your password?</p>
            <p class="text-sm text-gray-600">Enter your email and we'll send you a reset link.</p>
        </div>
        <form class="space-y-6 w-full max-w-sm " id="resetForm">
            @csrf
            <div class="space-y-6 mb-6">
                <div class="relative">
                    <input type="email" id="email" name="email" class="peer p-4 block w-full border border-gray-300 rounded-lg sm:text-sm placeholder:text-transparent focus:border-[#27D5E8] focus:ring-[#27D5E8] disabled:opacity-50 disabled:pointer-events-none
                    focus:pt-6
                    focus:pb-2
                    not-placeholder-shown:pt-6
                    not-placeholder-shown:pb-2
                    autofill:pt-6
                    autofill:pb-2" placeholder="you@email.com">
                    @if ($errors->has('email'))
                    <span class="mt-1 text-sm text-red-500 block">
                        {{ $errors->first('email') }}
                    </span>
                    @endif

                    <label for="email"
                        class="absolute top-0 start-0 p-4 h-full sm:text-sm truncate pointer-events-none transition ease-in-out duration-100 border border-transparent origin-[0_0] peer-disabled:opacity-50 peer-disabled:pointer-events-none
                      peer-focus:scale-90
                      peer-focus:translate-x-0.5
                      peer-focus:-translate-y-1.5
                      peer-focus:text-gray-500 dark:peer-focus:text-neutral-500
                      peer-not-placeholder-shown:scale-90
                      peer-not-placeholder-shown:translate-x-0.5
                      peer-not-placeholder-shown:-translate-y-1.5
                      peer-not-placeholder-shown:text-gray-500 dark:peer-not-placeholder-shown:text-neutral-500 dark:text-neutral-500">Email</label>
                </div>
            </div>
            <button type="submit"
                class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-[#0D1517] text-white hover:bg-[#182427] focus:outline-hidden cursor-pointer disabled:opacity-50">Send
                reset link</button>
            <p class="text-center text-sm text-gray-600 mt-4"><a href="/" class="text-[#27D5E8] underline">Back to
                    login</a></p>
        </form>
    </div>
</div>

@if(session('success'))
<script>
showModal("Berhasil!", "{{ session('success') }}");
</script>
@endif

@if(session('error'))
<script>
showModal("Gagal!", "{{ session('error') }}");
</script>
@endif

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
    const emailInput = document.getElementById("email");

    if (!form || !emailInput) {
        console.error("Form atau input email tidak ditemukan");
        return;
    }

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const email = emailInput.value.trim();
        if (!email) {
            showModal("Gagal", "Email wajib diisi");
            return;
        }

        const {
            error
        } = await supabase.auth.resetPasswordForEmail(email, {
            redirectTo: "{{ url('/') }}"
        });

        if (error) {
            showModal("Gagal", error.message);
        } else {
            showModal("Berhasil", "Link reset password telah dikirim ke email.");
            form.reset();
        }
    });
});
</script>

@endsection