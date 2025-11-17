<form method="GET" action="{{ url()->current() }}" class="rounded-2xl px-6 py-4 flex items-center gap-3" style="background: linear-gradient(to right, #122E32, #0B1A1C); box-shadow:0 4px 10px rgba(0, 224, 255, 0.15);">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-gray-300">
        <path fill-rule="evenodd" d="M10.5 3.75a6.75 6.75 0 104.238 12.027l4.743 4.742a.75.75 0 101.06-1.06l-4.742-4.743A6.75 6.75 0 0010.5 3.75zm-5.25 6.75a5.25 5.25 0 1110.5 0 5.25 5.25 0 01-10.5 0z" clip-rule="evenodd" />
    </svg>
    <input 
        type="text" 
        name="search"
        placeholder="Search" 
        value="{{ request('search') }}"
        class="w-full bg-transparent focus:outline-none" 
        style="color:#FFFFFF;" 
    />
    @if(request('search'))
        <a href="{{ url()->current() }}" class="text-gray-400 hover:text-white" title="Clear search">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
        </a>
    @endif
</form>

