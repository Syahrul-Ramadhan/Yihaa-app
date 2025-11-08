@extends('components._layouts.auth')
@section('content')
<div class="flex items-center md:h-auto min-h-screen overflow-hidden">
  <div class="bg-white w-full min-h-screen md:min-h-0 rounded-t-2xl md:rounded-2xl md:mx-auto mt-12 px-6 py-10 md:w-1/3 md:mt-0">
    <div class="text-gray-800 lg:text-center mb-6">
      <p class="font-bold text-2xl">Forgot your password?</p>
      <p class="text-sm text-gray-600">Enter your email and we'll send you a reset link.</p>
    </div>
    <form id="yihaa-forgot-form" class="space-y-6 w-full max-w-sm">
      @csrf
      <div class="space-y-6">
        <x-_ui.emailInput/>
      </div>
      <button type="button" id="forgot-btn" class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-[#0D1517] text-white hover:bg-[#182427] focus:outline-hidden cursor-pointer disabled:opacity-50">Send reset link</button>
      <p class="text-center text-sm text-gray-600 mt-4"><a href="/" class="text-[#27D5E8] underline">Back to login</a></p>
    </form>
  </div>
</div>
@endsection
