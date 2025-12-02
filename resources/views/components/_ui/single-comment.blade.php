<div class="flex gap-3 mb-4">
    <img src="{{ $c['user_avatar'] }}" class="w-10 h-10 rounded-full object-cover">
    <div>
        <p class="font-semibold">{{ $c['user_name'] }}</p>
        <p class="text-gray-300 text-sm">{{ $c['text'] }}</p>
        <span class="text-gray-500 text-xs">
            {{ \Carbon\Carbon::parse($c['created_at'])->diffForHumans() }}
        </span>
    </div>
</div>