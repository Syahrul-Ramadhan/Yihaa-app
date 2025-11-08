@extends('components._layouts.auth')
@section('content')
<div class="flex items-center md:h-auto min-h-screen overflow-hidden">
  <div class="bg-white w-full min-h-screen md:min-h-0 rounded-t-2xl md:rounded-2xl md:mx-auto mt-12 px-6 py-10 md:w-1/3 md:mt-0">
    <div class="text-gray-800 lg:text-center mb-6">
      <p class="font-bold text-2xl">Create your YIHAA account</p>
    </div>
    <form id="yihaa-register-form" class="space-y-6 w-full max-w-sm">
      @csrf
      <div class="space-y-6">
        <div class="relative">
          <input type="text" id="register-name" class="peer p-4 block w-full border border-gray-300 rounded-lg sm:text-sm placeholder:text-transparent focus:border-[#27D5E8] focus:ring-[#27D5E8]" placeholder="Your name">
          <label for="register-name" class="absolute top-0 start-0 p-4 h-full sm:text-sm truncate pointer-events-none transition duration-100 origin-[0_0]
            peer-focus:scale-90 peer-focus:-translate-y-1.5 peer-focus:text-gray-500
            peer-not-placeholder-shown:scale-90 peer-not-placeholder-shown:-translate-y-1.5 peer-not-placeholder-shown:text-gray-500">Name</label>
        </div>
        <x-_ui.emailInput/>
        <x-_ui.passwordInput/>
      </div>
      <button type="button" id="register-btn" class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-[#0D1517] text-white hover:bg-[#182427] focus:outline-hidden cursor-pointer disabled:opacity-50">Register</button>
      <p class="text-center text-sm text-gray-600 mt-4">Already have an account? <a href="/" class="text-[#27D5E8] underline">Login</a></p>
    </form>
  </div>
</div>
@endsection
@extends('components._layouts.auth')
@section('content')
    <div class="flex items-center md:h-auto min-h-screen overflow-hidden">
        <div class="bg-white w-full min-h-screen md:min-h-0 rounded-t-2xl md:rounded-2xl md:mx-auto mt-20 px-6 py-10 md:w-1/3 md:mt-0">
            <div class="welcome-message text-gray-800 lg:text-center mb-5">
                <p class="font-bold text-2xl sm:text">Sign Up</p>
            </div>

            <!-- Form Login -->
            <div class="flex items-center justify-center ">
                <form action="#" method="POST" class="space-y-6 mt-3 w-full max-w-sm">
                    <div class="max-w-sm space-y-6">
                        <div class="relative">
                            <input type="text" id="hs-floating-input-username" class="peer p-4 block w-full border border-gray-300 rounded-lg sm:text-sm placeholder:text-transparent focus:border-[#27D5E8] focus:ring-[#27D5E8] disabled:opacity-50 disabled:pointer-events-none
                            focus:pt-6
                            focus:pb-2
                            not-placeholder-shown:pt-6
                            not-placeholder-shown:pb-2
                            autofill:pt-6
                            autofill:pb-2" placeholder="your username.com">
                            <label for="hs-floating-input-username" class="absolute top-0 start-0 p-4 h-full sm:text-sm truncate pointer-events-none transition ease-in-out duration-100 border border-transparent origin-[0_0] peer-disabled:opacity-50 peer-disabled:pointer-events-none
                            peer-focus:scale-90
                            peer-focus:translate-x-0.5
                            peer-focus:-translate-y-1.5
                            peer-focus:text-gray-500 dark:peer-focus:text-neutral-500
                            peer-not-placeholder-shown:scale-90
                            peer-not-placeholder-shown:translate-x-0.5
                            peer-not-placeholder-shown:-translate-y-1.5
                            peer-not-placeholder-shown:text-gray-500 dark:peer-not-placeholder-shown:text-neutral-500 dark:text-neutral-500">Username</label>
                        </div>
                        <x-_ui.emailInput/>
                        <x-_ui.passwordInput/>
                    </div>
        
                    <div class="flex">
                        <div class="checkbox flex items-center">
                            <input type="checkbox" class="cursor-pointer shrink-0 mt-0.5 border-gray-200 rounded-sm text-[#27D5E8] focus:ring-[#198b97] checked:border-[#198b97] disabled:opacity-50 disabled:pointer-events-none id="hs-default-checkbox">
                            <label for="hs-default-checkbox" class="cursor-pointer text-sm text-gray-800 ms-3 dark:text-neutral-400">Remember me</label>
                        </div>
                    </div>
        
                    <button type="button" class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-[#0D1517] text-white hover:bg-[#182427] focus:outline-hidden focus:bg-red-800 cursor-pointer disabled:opacity-50 disabled:pointer-events-none">
                    Sign Up
                    </button>
                </form>
            </div>

            <div class="mt-6 text-center">
                <p class="text-base text-gray-800">Already have account? <a href="#" class="font-bold text-base text-[#27D5E8] underline hover:text-[#198b97]">Sign in</a></p>
            </div>
        </div>
    </div>
@endsection