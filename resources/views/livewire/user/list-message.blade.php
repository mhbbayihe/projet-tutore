<div class="dark:text-gray-100">
    <div class="pt-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Discussions</h1>
        <form wire:submit.prevent="loadFriendships">
            <div class="relative mb-6">
                <input
                    class="border-gray-300 dark:border-gray-600 rounded-full bg-gray-100 dark:bg-gray-700 w-full h-[35px] mt-4 pl-10 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:border-orange-500 focus:ring-0"
                    type="search"
                    wire:model.live="search"
                    name="search_friends"
                    id="search_friends"
                    placeholder="Search your friends..."
                />
                <svg class="absolute top-6 left-2 w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L19.71,19L18.29,20.41L13,15.14C11.88,15.85 10.5,16.29 9.5,16.29A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7.02,5 5,7.02 5,9.5C5,11.98 7.02,14 9.5,14C11.98,14 14,11.98 14,9.5C14,7.02 11.98,5 9.5,5Z" />
                </svg>
            </div>
        </form>
    </div>

    {{-- Change $users to $friendships --}}
    @forelse ($friendships as $friendship)
    <div class="flex items-center justify-between pt-4 ">
        {{-- The link should use $friendship->friend->id --}}
        <a href="{{ route('conversation', $friendship->friend->id) }}" class="block w-full p-2 -mx-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-150">
            <div class="flex items-center">
                <div class="relative">
                    <div>
                                    {{-- Check if a profile photo URL exists --}}
                                    @if (!empty($friendship->friend->profile)) {{-- Changed from $user->profile to $user->profile --}}
                                        <img
                                            class="border rounded-full w-[50px] h-[50px] object-cover mr-4"
                                            src="{{ Storage::url($friendship->friend->profile) }}"
                                            alt="{{ $friendship->friend->name }} Avatar"
                                        />
                                    @else
                                        {{-- Fallback: Display initials if no profile photo --}}
                                        <div
                                            class="flex items-center justify-center border rounded-full w-[50px] h-[50px] mr-4 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-bold text-lg uppercase"
                                            title="{{ $friendship->friend->name }}"
                                        >
                                            @php
                                                $nameParts = explode(' ', $friendship->friend->name);
                                                $initials = '';
                                                if (count($nameParts) > 0) {
                                                    $initials .= strtoupper(substr($nameParts[0], 0, 1));
                                                }
                                                if (count($nameParts) > 1) {
                                                    $initials .= strtoupper(substr(end($nameParts), 0, 1));
                                                } elseif (count($nameParts) == 1 && strlen($nameParts[0]) > 1) {
                                                    $initials = strtoupper(substr($nameParts[0], 0, 2));
                                                }
                                                if (empty($initials) && !empty($friendship->friend->name)) {
                                                    $initials = strtoupper(substr($friendship->friend->name, 0, 1));
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
                    <p class="text-base font-bold text-gray-900 dark:text-gray-100">{{ $friendship->friend->name }}</p>
                    {{-- If you had a placeholder like "Hello please I need your help today", remove it as it's not a conversation list --}}
                    <p class="text-xs text-gray-400">Friend connected</p> {{-- Or some other static text indicating it's a friend --}}
                </div>
            </div>
        </a>
    </div>
    @empty
        <span class="block px-4 py-6 text-center text-gray-500 dark:text-gray-500">{{ __('No friends found.') }}</span>
    @endforelse
</div>