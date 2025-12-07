@php
    $user = auth()->user();
    $notifications = $user ? \App\Models\Notification::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get() : collect();
    $unreadCount = $user ? \App\Models\Notification::where('user_id', $user->id)
        ->whereNull('read_at')
        ->count() : 0;
@endphp

<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-mayelia-500 rounded-md">
        <i class="fas fa-bell text-xl"></i>
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 block h-5 w-5 rounded-full bg-red-500 text-white text-xs flex items-center justify-center font-bold">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg z-50 border border-gray-200"
         style="display: none;">
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                @if($unreadCount > 0)
                    <button onclick="markAllAsRead()" class="text-sm text-mayelia-600 hover:text-mayelia-800">
                        Tout marquer comme lu
                    </button>
                @endif
            </div>
        </div>
        
        <div class="max-h-96 overflow-y-auto">
            @if($notifications->count() > 0)
                @foreach($notifications as $notification)
                    <div class="p-4 border-b border-gray-100 hover:bg-gray-50 {{ $notification->read_at ? '' : 'bg-mayelia-50' }}" 
                         onclick="markAsRead({{ $notification->id }})"
                         style="cursor: pointer;">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                @if($notification->type === 'oneci_carte_prete')
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-check-circle text-green-600"></i>
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-mayelia-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-info-circle text-mayelia-600"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $notification->title }}</p>
                                <p class="text-sm text-gray-500 mt-1">{{ $notification->message }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            @if(!$notification->read_at)
                                <div class="flex-shrink-0 ml-2">
                                    <span class="w-2 h-2 bg-mayelia-600 rounded-full inline-block"></span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-bell-slash text-3xl mb-2"></i>
                    <p>Aucune notification</p>
                </div>
            @endif
        </div>
        
        @if($notifications->count() > 0)
            <div class="p-4 border-t border-gray-200 text-center">
                <a href="#" class="text-sm text-mayelia-600 hover:text-mayelia-800">Voir toutes les notifications</a>
            </div>
        @endif
    </div>
</div>

<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function markAllAsRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>


