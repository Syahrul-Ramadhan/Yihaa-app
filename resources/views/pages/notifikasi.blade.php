@extends('components._layouts.home')
@section('content')
    <div class="max-w-3xl space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <h1 class="text-white text-2xl font-bold">Notifications</h1>
            
            @if(!empty($notifications))
            <form action="{{ route('notifikasi.markAllAsRead') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-[#2aa3ef] hover:bg-[#2aa3efcc] text-white text-sm font-semibold rounded-lg transition">
                    <i class="hgi hgi-stroke hgi-checkmark-circle-02 mr-1"></i>
                    Mark All as Read
                </button>
            </form>
            @endif
        </div>

        @if(session('success'))
        <div class="bg-green-500/20 border border-green-500/40 rounded-xl p-4">
            <p class="text-green-300">{{ session('success') }}</p>
        </div>
        @endif

        <!-- Notifications List -->
        @if(empty($notifications))
            <div class="text-center py-16">
                <div class="w-24 h-24 bg-gray-700/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="hgi hgi-stroke hgi-notification-02 text-5xl text-gray-500"></i>
                </div>
                <p class="text-gray-400 text-lg">No notifications yet</p>
                <p class="text-gray-500 text-sm mt-2">You'll see notifications for likes, comments, and team requests here</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($notifications as $notif)
                <div x-data="{ expanded: false }" 
                     class="rounded-2xl {{ $notif['is_read'] ? 'bg-gradient-to-r from-[#0d1f22] to-[#0a1517]' : 'bg-gradient-to-r from-[#122E32] to-[#0B1A1C] border-l-4 border-[#2aa3ef]' }} px-6 py-4 text-white shadow-md hover:shadow-lg transition">
                    
                    <div class="flex items-start justify-between gap-4">
                        <!-- Avatar & Content -->
                        <div class="flex gap-3 flex-1" @click="expanded = !expanded" style="cursor: pointer;">
                            <!-- Avatar -->
                            <img src="{{ $notif['users']['avatar_url'] ?? 'https://ui-avatars.com/api/?name=User' }}" 
                                 alt="User" 
                                 class="w-12 h-12 rounded-full object-cover flex-shrink-0">
                            
                            <!-- Message -->
                            <div class="flex-1 min-w-0">
                                <p class="font-medium {{ $notif['is_read'] ? 'text-gray-400' : 'text-white' }}">
                                    @if($notif['type'] === 'like')
                                        <i class="hgi hgi-stroke hgi-thumbs-up text-red-400 mr-1"></i>
                                        <span class="font-semibold text-[#2aa3ef]">{{ $notif['users']['name'] ?? 'Someone' }}</span> liked your post
                                    @elseif($notif['type'] === 'comment')
                                        <i class="hgi hgi-stroke hgi-comment-01 text-blue-400 mr-1"></i>
                                        <span class="font-semibold text-[#2aa3ef]">{{ $notif['users']['name'] ?? 'Someone' }}</span> commented on your post
                                    @elseif($notif['type'] === 'team_join_request')
                                        <i class="hgi hgi-stroke hgi-user-add-01 text-green-400 mr-1"></i>
                                        <span class="font-semibold text-[#2aa3ef]">{{ $notif['users']['name'] ?? 'Someone' }}</span> wants to join your team
                                    @elseif($notif['type'] === 'team_accepted')
                                        <i class="hgi hgi-stroke hgi-checkmark-circle-02 text-green-400 mr-1"></i>
                                        Your request to join <span class="font-semibold text-[#2aa3ef]">{{ $notif['teams']['team_name'] ?? 'a team' }}</span> has been accepted
                                    @else
                                        {{ $notif['message'] }}
                                    @endif
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($notif['created_at'])->diffForHumans() }}
                                </p>
                                
                                <!-- Expanded Details -->
                                <div x-show="expanded" x-collapse class="mt-3 pt-3 border-t border-gray-700">
                                    <p class="text-sm text-gray-400">{{ $notif['message'] }}</p>
                                    
                                    @if($notif['type'] === 'team_join_request' && !$notif['is_read'])
                                        <div class="flex gap-2 mt-3">
                                            <form action="{{ route('notifikasi.acceptTeam', [$notif['notification_id'], $notif['team_id']]) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-semibold rounded-lg transition">
                                                    <i class="hgi hgi-stroke hgi-checkmark-circle-02 mr-1"></i>
                                                    Accept
                                                </button>
                                            </form>
                                            <form action="{{ route('notifikasi.delete', $notif['notification_id']) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-lg transition">
                                                    <i class="hgi hgi-stroke hgi-cancel-01 mr-1"></i>
                                                    Decline
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Expand Icon -->
                            <div class="flex-shrink-0">
                                <i :class="expanded ? 'hgi-arrow-up-01' : 'hgi-arrow-down-01'" 
                                   class="hgi hgi-stroke text-gray-500 text-xl transition-transform"></i>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2 flex-shrink-0">
                            @if(!$notif['is_read'])
                            <form action="{{ route('notifikasi.markAsRead', $notif['notification_id']) }}" method="POST">
                                @csrf
                                <button type="submit" class="p-2 hover:bg-[#2aa3ef20] rounded-lg transition" title="Mark as read">
                                    <i class="hgi hgi-stroke hgi-checkmark-circle-02 text-[#2aa3ef]"></i>
                                </button>
                            </form>
                            @endif
                            
                            <form action="{{ route('notifikasi.delete', $notif['notification_id']) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 hover:bg-red-500/20 rounded-lg transition" title="Delete">
                                    <i class="hgi hgi-stroke hgi-delete-02 text-red-400"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
