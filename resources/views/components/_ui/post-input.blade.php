<form x-data="{ previewUrl: '' }" action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data"
    class="bg-[#2aa3ef07] rounded-2xl p-5 mb-6">

    @csrf

    <div class="flex space-x-3">
        <img src="{{  session('avatar_url') }}" class="w-10 h-10 rounded-full">
        <div class="flex-1">

            <textarea name="caption" placeholder="What’s new?"
                class="bg-transparent w-full resize-none text-gray-200 outline-none"></textarea>

            <div x-show="previewUrl" x-cloak class="mt-4 relative">
                <img :src="previewUrl" class="rounded-lg w-full object-cover max-h-60">

                <button type="button" @click="previewUrl = ''; $refs.imageInput.value = null"
                    class="absolute top-2 right-2 bg-black bg-opacity-75 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm">
                    &times;
                </button>
            </div>

            <div class="flex justify-between items-center mt-3">
                <div class="flex space-x-3 text-gray-400">
                    <label for="imageInput" class="cursor-pointer">
                        <i class="hgi hgi-stroke hgi-image-add-02 text-2xl"></i>
                    </label>

                    <input type="file" id="imageInput" name="image" accept="image/*" class="hidden" x-ref="imageInput"
                        @change="
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                previewUrl = e.target.result;
                            };
                            reader.readAsDataURL($event.target.files[0]);
                        ">
                </div>

                <button type="submit"
                    class="bg-[#4FD8E0] text-black px-5 py-2 rounded-xl hover:bg-[#2e9da3] transition cursor-pointer">
                    Posting
                </button>
            </div>
        </div>
    </div>
</form>