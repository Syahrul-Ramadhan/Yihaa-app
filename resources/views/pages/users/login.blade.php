@extends('components._layouts.auth')
@section('content')
    <div class="flex items-center md:h-auto min-h-screen overflow-hidden">
        <div class="bg-white w-full min-h-screen md:min-h-0 rounded-t-2xl md:rounded-2xl md:mx-auto mt-20 px-6 py-10 md:w-1/3 md:mt-0">
            <div class="welcome-message text-gray-800 lg:text-center mb-5">
                <p class="font-bold text-2xl sm:text">Sign in to YIHAA</p>
            </div>

            <!-- Form Login -->
            <div class="flex items-center justify-center ">
                <form action="#" method="POST" class="space-y-6 mt-3 w-full max-w-sm">
                    <div class="max-w-sm space-y-6">
                        <x-_ui.emailInput/>
                        <x-_ui.passwordInput/>
                    </div>
        
                    <div class="flex justify-end">
                            <a href="#" class="text-base text-[#27D5E8] underline hover:text-[#198b97]">Forgot password?</a>
                    </div>
        
                    <button type="button" class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-[#0D1517] text-white hover:bg-[#182427] focus:outline-hidden focus:bg-red-800 cursor-pointer disabled:opacity-50 disabled:pointer-events-none">
                    Sign In
                    </button>
                </form>
            </div>

            <div class="mt-6 text-center">
                <p class="text-base text-gray-800">Don't have an account? <a href="#" class="font-bold text-base text-[#27D5E8] underline hover:text-[#198b97]">Sign up</a></p>
            </div>
        </div>
    </div>
@endsection