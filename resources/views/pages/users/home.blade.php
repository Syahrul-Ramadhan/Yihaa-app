@extends('components._layouts.home')
@section('content')
    <div class="max-w-2xl space-y-6">
        <div class="relative">
            <input type="text" class="peer py-2.5 sm:py-3 px-4 ps-11 block w-full bg-gradient-to-l from-[#163F44] to-[#020C0D] border border-gray-600 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none" placeholder="Search">
            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-4 peer-disabled:opacity-50 peer-disabled:pointer-events-none">
                <i class="hgi hgi-stroke hgi-search-01"></i>
            </div>
        </div>
    </div>
@endsection