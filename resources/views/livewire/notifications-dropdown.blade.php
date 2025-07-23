<div class="relative" x-data="{ open: @entangle('isOpen') }" @click.outside="open = false">
    <flux:navlist.item icon="bell" @click="open = !open" class="mb-4" wire:navigate>
        {{ __('Notifications') }}
        @if ($unreadCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2">{{ $unreadCount }}</span>
        @endif
    </flux:navlist.item>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute left-5 mt-2 w-80 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 z-50 origin-top-right"
         role="menu" aria-orientation="vertical" aria-labelledby="notification-menu">
        <div class="py-1" role="none">
            <div class="block px-4 py-2 text-xs text-gray-400">
                Notifications
            </div>
            @forelse ($notifications as $notification)
                <a href="{{ $notification->data['link'] ?? '#' }}" wire:click="markAsRead('{{ $notification->id }}')" class="flex items-center px-4 py-3 border-b border-gray-100 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 {{ $notification->read_at ? 'text-gray-500' : 'text-gray-800 dark:text-gray-100 font-semibold' }}" role="menuitem">
                    <i class="mdi mdi-{{ $notification->data['icon'] ?? 'information' }} text-lg mr-2 text-{{ $notification->data['color'] ?? 'blue' }}-500"></i>
                    <p class="text-sm">
                        {{ $notification->data['message'] }}
                        <span class="block text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</span>
                    </p>
                </a>
            @empty
                <div class="block px-4 py-2 text-sm text-gray-500">
                    No new notification
                </div>
            @endforelse
            @if ($notifications->count())
                <button wire:click="markAllAsRead" class="block w-full text-left px-4 py-2 text-sm text-indigo-600 dark:text-indigo-400 hover:bg-gray-50 dark:hover:bg-gray-600">
                    Mark all as read
                </button>
            @endif
        </div>
    </div>
</div>
