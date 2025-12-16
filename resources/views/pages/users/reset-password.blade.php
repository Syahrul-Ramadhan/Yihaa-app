@extends('components._layouts.auth')
@section('content')
<div class="flex items-center md:h-auto min-h-screen overflow-hidden">
    <div
        class="bg-white w-full min-h-screen md:min-h-0 rounded-t-2xl md:rounded-2xl md:mx-auto mt-12 px-6 py-10 md:w-1/3 md:mt-0">
        <div class="text-gray-800 lg:text-center mb-6">
            <p class="font-bold text-2xl">Reset your password</p>
        </div>
        <form class="space-y-6 w-full max-w-sm" id="newPasswordForm">
            @csrf
            <div class="space-y-6">
                <div class="relative">
                    <input type="password" id="password"
                        class="peer p-4 block w-full border border-gray-300 rounded-lg sm:text-sm placeholder:text-transparent focus:border-[#27D5E8] focus:ring-[#27D5E8]"
                        placeholder="New password">
                    <label for="reset-password-value"
                        class="absolute top-0 start-0 p-4 h-full sm:text-sm truncate pointer-events-none transition duration-100 origin-[0_0]
            peer-focus:scale-90 peer-focus:-translate-y-1.5 peer-focus:text-gray-500
            peer-not-placeholder-shown:scale-90 peer-not-placeholder-shown:-translate-y-1.5 peer-not-placeholder-shown:text-gray-500">New
                        password</label>
                </div>
                <div class="relative">
                    <input type="password" id="confirm"
                        class="peer p-4 block w-full border border-gray-300 rounded-lg sm:text-sm placeholder:text-transparent focus:border-[#27D5E8] focus:ring-[#27D5E8]"
                        placeholder="New password">
                    <label for="reset-password-value"
                        class="absolute top-0 start-0 p-4 h-full sm:text-sm truncate pointer-events-none transition duration-100 origin-[0_0]
            peer-focus:scale-90 peer-focus:-translate-y-1.5 peer-focus:text-gray-500
            peer-not-placeholder-shown:scale-90 peer-not-placeholder-shown:-translate-y-1.5 peer-not-placeholder-shown:text-gray-500">Confirm
                        password</label>
                </div>
            </div>
            <button type="submit"
                class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-[#0D1517] text-white hover:bg-[#182427] focus:outline-hidden cursor-pointer disabled:opacity-50">Update
                password</button>
            <p class="text-center text-sm text-gray-600 mt-4"><a href="/" class="text-[#27D5E8] underline">Back to
                    login</a></p>
        </form>
    </div>
</div>

@if(session('error'))
<script>
showModal("Gagal!", "{{ session('error') }}");
</script>
@endif

<script type="module">
import {
    createClient
} from 'https://esm.sh/@supabase/supabase-js';

const supabase = createClient(
    "{{ env('SUPABASE_URL') }}",
    "{{ env('SUPABASE_ANON_KEY') }}"
);

document.getElementById("newPasswordForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    const password = document.getElementById("password").value;
    const confirm = document.getElementById("confirm").value;

    if (password !== confirm) {
        alert("Password tidak sama");
        return;
    }

    const {
        error
    } = await supabase.auth.updateUser({
        password
    });

    if (error) {
        alert(error.message);
    } else {
        alert("Password berhasil diubah");
        window.location.href = "/";
    }
});
</script>
@endsection