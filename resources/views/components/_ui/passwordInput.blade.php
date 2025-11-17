<div class="relative">
  <input 
    id="password-input" 
    type="password" 
    name="password" 
    class="py-3 px-4 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500" 
    placeholder="Enter password"
    required
  >

  @if ($errors->has('password'))
        <span class="mt-1 text-sm text-red-500 block">
            {{ $errors->first('password') }}
        </span>
  @endif

  <button 
    type="button" 
    id="toggle-password-btn"
    class="absolute inset-y-0 end-0 flex items-center p-3 cursor-pointer"
  >
    <!-- Default icon (eye-off) -->
    <i id="icon-eye-off" class="hgi hgi-stroke hgi-view-off"></i>

    <!-- Icon show (hidden by default) -->
    <i id="icon-eye" class="hgi hgi-stroke hgi-view hidden"></i>
  </button>
</div>

<script>
  const passwordInput = document.getElementById("password-input");
  const toggleBtn = document.getElementById("toggle-password-btn");
  const iconEye = document.getElementById("icon-eye");
  const iconEyeOff = document.getElementById("icon-eye-off");

  toggleBtn.addEventListener("click", () => {
    const isPassword = passwordInput.type === "password";
    passwordInput.type = isPassword ? "text" : "password";
    iconEye.classList.toggle("hidden");
    iconEyeOff.classList.toggle("hidden");
  });
</script>
