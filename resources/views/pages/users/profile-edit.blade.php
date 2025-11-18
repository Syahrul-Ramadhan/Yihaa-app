@extends('components._layouts.home')

@section('content')
<div class="min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('profile.index') }}" class="text-gray-400 hover:text-white mb-4 inline-flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Back to Profile
            </a>
            <h1 class="text-3xl font-bold mt-4">Edit Profile</h1>
            <p class="text-gray-400 mt-2">Update your profile information</p>
        </div>

        <!-- Form -->
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Avatar Upload -->
            <div class="bg-gradient-to-br from-[#163F44] to-[#0a2020] rounded-2xl p-6 border border-gray-700">
                <h2 class="text-xl font-semibold mb-4 text-white">Profile Picture</h2>
                <div class="flex items-center gap-6">
                    <!-- Current Avatar -->
                    <div class="relative">
                        @if($user['avatar_url'])
                            <img id="avatar-preview" src="{{ $user['avatar_url'] }}" alt="{{ $user['name'] }}" 
                                class="w-24 h-24 rounded-full object-cover border-4 border-[#2aa3ef]">
                        @else
                            <div id="avatar-preview" class="w-24 h-24 rounded-full bg-gradient-to-br from-[#2aa3ef] to-[#1e7bb8] flex items-center justify-center border-4 border-[#2aa3ef]">
                                <span class="text-3xl font-bold">{{ strtoupper(substr($user['name'], 0, 1)) }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Upload Button -->
                    <div class="flex-1">
                        <label for="avatar" class="cursor-pointer inline-block px-6 py-3 bg-[#252525] hover:bg-[#2a2a2a] rounded-xl border border-gray-700 hover:border-[#2aa3ef] transition-all duration-200">
                            <i class="fas fa-upload mr-2"></i>
                            Upload New Photo
                        </label>
                        <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden">
                        <p class="text-gray-400 text-sm mt-2">JPG, PNG or GIF. Max 5MB.</p>
                        @error('avatar')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="bg-gradient-to-br from-[#163F44] to-[#0a2020] rounded-2xl p-6 border border-gray-700 space-y-6">
                <h2 class="text-xl font-semibold text-white">Basic Information</h2>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user['name']) }}" required
                        class="w-full px-4 py-3 bg-[#252525] border border-gray-700 rounded-xl text-white focus:outline-none focus:border-[#2aa3ef] transition-all">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email (Read Only) -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                        Email Address
                    </label>
                    <div class="relative">
                        <input type="email" id="email" value="{{ $user['email'] }}" readonly
                            class="w-full px-4 py-3 bg-[#1a1a1a] border border-gray-800 rounded-xl text-gray-400 cursor-not-allowed">
                        <i class="fas fa-lock absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-600"></i>
                    </div>
                    <p class="text-gray-500 text-xs mt-1">Email cannot be changed</p>
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-300 mb-2">
                        Phone Number
                    </label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $profile['phone'] ?? '') }}"
                        class="w-full px-4 py-3 bg-[#252525] border border-gray-700 rounded-xl text-white focus:outline-none focus:border-[#2aa3ef] transition-all"
                        placeholder="+62 812 3456 7890">
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- University -->
                <div>
                    <label for="university" class="block text-sm font-medium text-gray-300 mb-2">
                        University
                    </label>
                    <input type="text" id="university" name="university" value="{{ old('university', $profile['university'] ?? '') }}"
                        class="w-full px-4 py-3 bg-[#252525] border border-gray-700 rounded-xl text-white focus:outline-none focus:border-[#2aa3ef] transition-all"
                        placeholder="e.g., Universitas Indonesia">
                    @error('university')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Program Study -->
                <div>
                    <label for="program_study" class="block text-sm font-medium text-gray-300 mb-2">
                        Program Study
                    </label>
                    <input type="text" id="program_study" name="program_study" value="{{ old('program_study', $profile['program_study'] ?? '') }}"
                        class="w-full px-4 py-3 bg-[#252525] border border-gray-700 rounded-xl text-white focus:outline-none focus:border-[#2aa3ef] transition-all"
                        placeholder="e.g., Computer Science">
                    @error('program_study')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- User Type -->
                <div>
                    <label for="user_type" class="block text-sm font-medium text-gray-300 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="user_type" name="user_type" required
                        class="w-full px-4 py-3 bg-[#252525] border border-gray-700 rounded-xl text-white focus:outline-none focus:border-[#2aa3ef] transition-all">
                        <option value="mahasiswa" {{ old('user_type', $profile['user_type'] ?? 'mahasiswa') == 'mahasiswa' ? 'selected' : '' }}>
                            Mahasiswa
                        </option>
                        <option value="dosen" {{ old('user_type', $profile['user_type'] ?? '') == 'dosen' ? 'selected' : '' }}>
                            Dosen
                        </option>
                        <option value="alumni" {{ old('user_type', $profile['user_type'] ?? '') == 'alumni' ? 'selected' : '' }}>
                            Alumni
                        </option>
                    </select>
                    @error('user_type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Additional Information -->
            <div class="bg-gradient-to-br from-[#163F44] to-[#0a2020] rounded-2xl p-6 border border-gray-700 space-y-6">
                <h2 class="text-xl font-semibold text-white">Additional Information</h2>

                <!-- Bio -->
                <div>
                    <label for="bio" class="block text-sm font-medium text-gray-300 mb-2">
                        Bio
                    </label>
                    <textarea id="bio" name="bio" rows="4"
                        class="w-full px-4 py-3 bg-[#252525] border border-gray-700 rounded-xl text-white focus:outline-none focus:border-[#2aa3ef] transition-all resize-none"
                        placeholder="Tell us about yourself...">{{ old('bio', $profile['bio'] ?? '') }}</textarea>
                    <p class="text-gray-500 text-xs mt-1">Brief description for your profile</p>
                    @error('bio')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role in Team -->
                <div>
                    <label for="role_in_team" class="block text-sm font-medium text-gray-300 mb-2">
                        Role in Team
                    </label>
                    <input type="text" id="role_in_team" name="role_in_team" value="{{ old('role_in_team', $profile['role_in_team'] ?? '') }}"
                        class="w-full px-4 py-3 bg-[#252525] border border-gray-700 rounded-xl text-white focus:outline-none focus:border-[#2aa3ef] transition-all"
                        placeholder="e.g., UI/UX Designer, Backend Developer">
                    <p class="text-gray-500 text-xs mt-1">Your primary role in team collaborations</p>
                    @error('role_in_team')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-4 pt-4">
                <a href="{{ route('profile.index') }}" 
                    class="px-6 py-3 bg-[#252525] hover:bg-[#2a2a2a] text-white rounded-xl font-medium transition-all duration-200 border border-gray-700">
                    Cancel
                </a>
                <button type="submit" 
                    class="px-6 py-3 bg-[#2aa3ef] hover:bg-[#1e7bb8] text-white rounded-xl font-medium transition-all duration-200 flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Avatar Preview
    document.getElementById('avatar').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('avatar-preview');
                if (preview.tagName === 'IMG') {
                    preview.src = e.target.result;
                } else {
                    // Replace div with img
                    const img = document.createElement('img');
                    img.id = 'avatar-preview';
                    img.src = e.target.result;
                    img.alt = 'Avatar Preview';
                    img.className = 'w-24 h-24 rounded-full object-cover border-4 border-[#2aa3ef]';
                    preview.parentNode.replaceChild(img, preview);
                }
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
