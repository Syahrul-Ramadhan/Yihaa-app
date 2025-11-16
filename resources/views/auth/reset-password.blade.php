@extends('components._layouts.auth')
@section('content')
<div class="flex items-center min-h-screen">
  <div class="bg-white w-full md:w-1/3 rounded-2xl p-6 mx-auto">
    <h2 class="font-bold text-xl mb-4">Reset Password</h2>

    <form action="{{ route('password.update') }}" method="POST">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">

      <input type="password" name="password" placeholder="Password baru"
             class="w-full p-3 border rounded mb-4" required>

      <input type="password" name="password_confirmation" placeholder="Konfirmasi password"
             class="w-full p-3 border rounded mb-4" required>

      <button type="submit" class="w-full p-3 bg-black text-white rounded">
        Update Password
      </button>
    </form>
  </div>
</div>

@if(session('success'))
<script>
  showModal("Berhasil!", "{{ session('success') }}", () => {
    window.location.href = "{{ session('redirect_to') ?? '/' }}";
  });
</script>
@endif

@if(session('error'))
<script>
  showModal("Terjadi Kesalahan", "{{ session('error') }}");
</script>
@endif

@endsection
