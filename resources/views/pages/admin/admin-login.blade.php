@extends('components._layouts.auth')
@section('content')
<div class="flex items-center md:h-auto min-h-screen overflow-hidden">
  <div class="bg-white w-full min-h-screen md:min-h-0 rounded-t-2xl md:rounded-2xl md:mx-auto mt-12 px-6 py-10 md:w-1/3 md:mt-0">
    <div class="text-gray-800 lg:text-center mb-6">
      <p class="font-bold text-2xl">Admin Login</p>
      <p class="text-sm text-gray-600 mt-2">Access restricted to administrators only</p>
    </div>

    @if(session('error'))
      <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @if(session('success'))
      <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
        {{ session('success') }}
      </div>
    @endif

    @if($errors->any())
      <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
        <ul class="list-disc list-inside">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('admin.login.process') }}" method="POST" class="space-y-6 w-full max-w-sm">
      @csrf
      <div class="space-y-6">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
          <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#27D5E8] focus:border-transparent" placeholder="Enter your email">
        </div>
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
          <input type="password" id="password" name="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#27D5E8] focus:border-transparent" placeholder="Enter your password">
        </div>
      </div>
      <button type="submit" class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-[#0D1517] text-white hover:bg-[#182427] focus:outline-hidden cursor-pointer disabled:opacity-50">
        Login as Admin
      </button>
      <p class="text-center text-sm text-gray-600 mt-4">
        <a href="{{ route('login') }}" class="text-[#27D5E8] underline">Back to User Login</a>
      </p>
    </form>
  </div>
</div>
@endsection
