<div>
    <form class='pb-6'>
        <div class="relative">
            <flux:input
                type="text"
                name="search"
                placeholder="Search an user by name, email, surname or phone"
                class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                wire:model.live.debounce.500ms="search"
                icon="magnifying-glass"
                icon-position="leading"
                icon-class="text-gray-400"
                icon-size="1.5"
            />
        </div>
    </form>

    <div class="flex justify-between items-end mb-4">
        <h1 class="text-lg font-bold">List of user</h1>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 fixed right-4 top-4 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 fixed right-4 top-4 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="space-y-4">
        @forelse ($users as $user)
            <div class="flex items-center justify-between px-4 py-2 bg-gray-50 dark:bg-gray-700 shadow-sm border border-gray-300 dark:border-gray-600 rounded-lg">
                <div class="flex items-center">
                    <div>
                        <div>
                            @if (!empty($user->profile))
                                <img
                                    class="border rounded-full w-[50px] h-[50px] object-cover mr-4"
                                    src="{{ Storage::url($user->profile) }}"
                                    alt="{{ $user->name }} Avatar"
                                />
                            @else
                                <div
                                    class="flex items-center justify-center border rounded-full w-[50px] h-[50px] mr-4 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-bold text-lg uppercase"
                                    title="{{ $user->name }}"
                                >
                                    @php
                                        $nameParts = explode(' ', $user->name);
                                        $initials = '';
                                        if (count($nameParts) > 0) {
                                            $initials .= strtoupper(substr($nameParts[0], 0, 1));
                                        }
                                        if (count($nameParts) > 1) {
                                            $initials .= strtoupper(substr(end($nameParts), 0, 1));
                                        } elseif (count($nameParts) == 1 && strlen($nameParts[0]) > 1) {
                                            $initials = strtoupper(substr($nameParts[0], 0, 2));
                                        }
                                        if (empty($initials) && !empty($user->name)) {
                                            $initials = strtoupper(substr($user->name, 0, 1));
                                        } elseif (empty($initials)) {
                                            $initials = '?';
                                        }
                                    @endphp
                                    {{ $initials }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div>
                        <p class="text-base font-bold text-gray-900 dark:text-gray-100">{{ $user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $user->surname }}</p>
                    </div>
                </div>
                <div>
                    <flux:button class="px-3 py-2 text-sm rounded-lg"
                        wire:navigate
                        href="{{ route('admins.user.view', $user->id) }}">
                            <i class="mdi mdi-eye text-blue-500 text-lg"></i>
                    </flux:button>

                    <flux:button wire:click="toggleUserBlock({{ $user->id }})" class="px-3 py-2 text-sm rounded-lg">
                        @if ($user->status === 1)
                            <i class="mdi mdi-lock-open-variant text-green-500 text-lg"></i>
                        @else
                            <i class="mdi mdi-lock text-red-500 text-lg"></i>
                        @endif
                    </flux:button>

                </div>
                
            </div>
        @empty
            <div class="text-center text-gray-500 dark:text-gray-400 p-8">
                No user found.
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $users->links() }}
    </div>
</div>