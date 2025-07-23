<div>
    <flux:sidebar stashable class="border-e fixed border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900 h-screen">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('home') }}" class=" mt-6 flex items-center font-bold text-xl space-x-2 rtl:space-x-reverse" wire:navigate>
            Codev
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group >
                <flux:navlist.item icon="home" class="mb-4" :href="route('home')" :current="request()->routeIs('home')" wire:navigate>{{ __('Home') }}</flux:navlist.item>
                <flux:navlist.item icon="magnifying-glass" class="mb-4" :href="route('search')" :current="request()->routeIs('search')" wire:navigate>{{ __('Search') }}</flux:navlist.item>
                <div x-data="{ open: false }" class="mb-4">
                    <div @click="open = ! open" class="flex items-center w-full cursor-pointer focus:outline-none">
                        <flux:navlist.item icon="users" :active="request()->routeIs('admins.communities.*')">
                            <span class="flex-1 text-left">{{ __('Communities') }}</span>
                            <i data-feather="chevron-down" class="feather-sm"></i>
                        </flux:navlist.item>
                    </div>
                    <div x-show="open" class="mt-1 ml-6 origin-top-left bg-white rounded-md shadow-lg dark:bg-gray-800" @click.away="open = false">
                        @forelse ($communities as $community)
                            <a href="{{ route('community', $community->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700" wire:navigate>{{ $community->name }}</a>
                        @empty
                            <span class="block px-4 py-2 text-sm text-gray-500 dark:text-gray-500">{{ __('No communities yet.') }}</span>
                        @endforelse
                    </div>
                </div>
                <div x-data="{ open: false }" class="mb-4">
                    <div @click="open = true" class="flex items-center w-full cursor-pointer focus:outline-none">
                        <flux:navlist.item icon="user-circle" :active="request()->routeIs('admins.communities.*')">
                            <span class="flex-1 text-left">{{ __('Friends') }}</span>
                            <i data-feather="chevron-down" class="feather-sm"></i>
                        </flux:navlist.item>
                    </div>
                    <div x-show="open" class="fixed h-screen bg-white rounded-br-2xl rounded-tr-2xl border-l-2 border-r-2 border-gray-200 top-0 left-0 z-1 box-border  w-[100%] pt-10 px-4" @click.away="open = false">
                        <div class="flex flex-col">
                            <h1 class="text-2xl font-bold">Friends</h1>
                            <form wire:submit.prevent="render" class=''>
                                <div class="relative">
                                    <input
                                        class="border w-full rounded-full h-[30px] pl-10 p-2 border-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-0 focus:border-blue-500"
                                        type="search"
                                        wire:model.live.debounce.300ms="search" {{-- Livewire search binding --}}
                                        placeholder="Find friends"
                                    />
                                    <i class="absolute top-1 left-2 mdi mdi-magnify text-xl"></i>
                                </div>
                            </form>
                            <div x-data="{ openModal1: true, openModal2: false }">
                                <div class="flex items-center justify-between my-4">
                                    <button @click="openModal1 = true; openModal2 = false" class="text-base font-bold bg-blue-400 text-white px-4 py-1 rounded-lg">List</button>
                                    <button @click="openModal1 = false; openModal2 = true" class="text-base font-bold bg-blue-400 text-white px-4 py-1 rounded-lg">Invitations</button>
                                </div>
                                <div class="h-screen overflow-y-auto no-scrollbar">
                                    <div x-show="openModal1">
                                        @forelse ($users as $user)
                                            <div class="pt-4 bg-gray-100 rounded-lg px-4 mt-2">
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
                                                        <p class="text-xs text-gray-400">Followed by Heneg Bayihe + 1</p>
                                                    </div>
                                                </div>
                                                <div class="flex justify-between py-1">
                                                    <a class="text-sm bg-blue-600 text-white p-1 rounded-lg " href="{{ route('users.account.view', $user->id ) }}">view <i class="mdi mdi-eye"></i></a>
                                                </div>
                                            </div>
                                        @empty
                                            <span class="block px-4 py-2 text-sm text-gray-500 dark:text-gray-500">{{ __('No user found.') }}</span>
                                        @endforelse
                                    </div>
                                    <div x-show="openModal2">
                                        @forelse ($lists as $list)
                                            <div class="pt-4 bg-gray-100 rounded-lg px-4 mt-2">
                                                <div class="flex items-center">
                                                    <div>
                                                        {{-- Check if a profile photo URL exists --}}
                                                        @if (!empty($list->sender->profile)) {{-- Changed from $user->profile to $user->profile --}}
                                                            <img
                                                                class="border rounded-full w-[50px] h-[50px] object-cover mr-4"
                                                                src="{{ Storage::url($list->sender->profile) }}"
                                                                alt="{{ $list->sender->name }} Avatar"
                                                            />
                                                        @else
                                                            {{-- Fallback: Display initials if no profile photo --}}
                                                            <div
                                                                class="flex items-center justify-center border rounded-full w-[50px] h-[50px] mr-4 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-bold text-lg uppercase"
                                                                title="{{ $list->sender->name }}"
                                                            >
                                                                @php
                                                                    $nameParts = explode(' ', $list->sender->name);
                                                                    $initials = '';
                                                                    if (count($nameParts) > 0) {
                                                                        $initials .= strtoupper(substr($nameParts[0], 0, 1));
                                                                    }
                                                                    if (count($nameParts) > 1) {
                                                                        $initials .= strtoupper(substr(end($nameParts), 0, 1));
                                                                    } elseif (count($nameParts) == 1 && strlen($nameParts[0]) > 1) {
                                                                        $initials = strtoupper(substr($nameParts[0], 0, 2));
                                                                    }
                                                                    if (empty($initials) && !empty($list->sender->name)) {
                                                                        $initials = strtoupper(substr($list->sender->name, 0, 1));
                                                                    } elseif (empty($initials)) {
                                                                        $initials = '?';
                                                                    }
                                                                @endphp
                                                                {{ $initials }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-bold">{{ $list->sender->name }}</p>
                                                        <p class="text-xs text-gray-400">Followed by Heneg Bayihe + 1</p>
                                                    </div>
                                                </div>
                                                <div class="flex justify-between py-1">
                                                    <a class="text-sm bg-blue-600 text-white p-1 rounded-lg " href="{{ route('users.account.view', $list->sender->id ) }}">view <i class="mdi mdi-eye"></i></a>
                                                    <form action="{{ route('users.invitation.accept', $list->id) }}" method="post">
                                                        @csrf
                                                        @method('POST')
                                                        <button type="submit" class="text-sm bg-blue-600 text-white p-1 rounded-lg ">Accept</button>
                                                        <button type="submit" class="text-sm bg-blue-600 text-white p-1 rounded-lg ">Refuse</button>
                                                    </form>
                                                </div>
                                            </div>
                                        @empty
                                            <span class="block px-4 py-2 text-sm text-gray-500 dark:text-gray-500">{{ __('No user found.') }}</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="absolute text-2xl font-bold right-3 top-3" @click="open = false">&times;</button>
                    </div>
                </div>
                <flux:navlist.item icon="envelope" class="mb-4" :href="route('message')" :current="request()->routeIs('message')" wire:navigate>{{ __('Messages') }}</flux:navlist.item>
                <livewire:notifications-dropdown />
                <flux:navlist.item icon="share" class="mb-4" :href="route('favory')" :current="request()->routeIs('favory')" wire:navigate>{{ __('Favories') }}</flux:navlist.item>
                <flux:navlist.item icon="user-circle" class="mb-4" :href="route('users.account')" :current="request()->routeIs('users.account')" wire:navigate>{{ __('Account') }}</flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />

        <flux:dropdown class="hidden lg:block" position="bottom" align="start">
            <flux:profile
                :name="auth()->user()->name"
                :initials="auth()->user()->initials()"
                icon:trailing="chevrons-up-down"
            />

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                >
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('users.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />
        <flux:link >
            <flux:icon name="user-circle"></flux:icon>
        </flux:link>
        <flux:dropdown position="top" align="end">
            <flux:profile
                :initials="auth()->user()->initials()"
                icon-trailing="chevron-down"
            />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                >
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('users.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>


    @fluxScripts
</div>