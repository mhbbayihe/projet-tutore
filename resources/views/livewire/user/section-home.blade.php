<div class="dark:text-gray-100 bg-white p-4">
    <div class="flex flex-col">
        <h1 class="text-xl font-bold my-3">Dou you nkow?</h1>
        @if (session()->has('error'))
            <div class="bg-red-100 fixed right-4 top-4 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                {{ session('error') }}
            </div>
        @endif
        @if (session()->has('succes'))
            <div class="bg-green-100 fixed right-4 top-4 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                {{ session('succes') }}
            </div>
        @endif
        <form wire:submit.prevent="render" class=''> {{-- wire:submit.prevent pour une soumission potentielle, mais wire:model.live gère la réactivité --}}
            <div class="relative">
                <input
                    class="border-1 w-full rounded-full h-[30px] pl-10 p-2 border-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-0 focus:border-blue-500"
                    type="search"
                    wire:model.live="search" {{-- C'est la ligne clé pour la recherche en temps réel --}}
                    placeholder="Find friends"
                />
                {{-- Replace <i> with SVG for better compatibility and styling --}}
                <svg class="absolute top-1/2 -translate-y-1/2 left-2 w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L19.71,19L18.29,20.41L13,15.14C11.88,15.85 10.5,16.29 9.5,16.29A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7.02,5 5,7.02 5,9.5C5,11.98 7.02,14 9.5,14C11.98,14 14,11.98 14,9.5C14,7.02 11.98,5 9.5,5Z" />
                </svg>
            </div>
        </form>
        <div>
            <div class="flex items-center justify-between my-4">
                {{-- This div was empty, consider putting something here like a filter toggle or just remove it if not needed --}}
            </div>
            <div class="h-screen overflow-y-auto no-scrollbar">
                <div>
                    @forelse ($users as $user)
                        <div class="pt-4 bg-gray-100 dark:bg-gray-800 rounded-lg px-4 mt-2">
                            <div class="flex items-center">
                                <div>
                                    {{-- Check if a profile photo URL exists --}}
                                    @if (!empty($user->profile)) {{-- Changed from $user->profile to $user->profile --}}
                                        <img
                                            class="border rounded-full w-[50px] h-[50px] object-cover mr-4"
                                            src="{{ Storage::url($user->profile) }}"
                                            alt="{{ $user->name }} Avatar"
                                        />
                                    @else
                                        {{-- Fallback: Display initials if no profile photo --}}
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
                                <div>
                                    <p class="text-sm font-bold">{{ $user->name }}</p>
                                    {{-- "Followed by Heneg Bayihe + 1" might need dynamic content if it's not static --}}
                                    <p class="text-xs text-gray-400">Followed by Heneg Bayihe + 1</p>
                                </div>
                            </div>
                            <div class="flex justify-between py-2">
                                <a class="text-sm bg-blue-600 text-white p-1 px-2 rounded-lg " href="{{ route('users.account.view', $user->id ) }}">view <i class="mdi mdi-eye"></i></a>
                                <form action="{{ route('users.invitation.send', $user->id) }}" method="post">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="text-sm bg-blue-600 text-white p-1 px-2 rounded-lg ">Send invitation</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <span class="block px-4 py-2 text-sm text-gray-500 dark:text-gray-500">{{ __('No user found with shared communities or matching your search.') }}</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>