{{-- resources/views/livewire/admin-list.blade.php --}}
<div>
    {{-- Search Input --}}
    <form class='pb-6'>
        <div class="relative">
            <flux:input
                type="text"
                name="search"
                placeholder="Search an admin by name, email, surname or phone"
                class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                wire:model.live.debounce.500ms="search"
                icon="magnifying-glass"
                icon-position="leading"
                icon-class="text-gray-400"
                icon-size="1.5"
            />
        </div>
    </form>

    {{-- Optional: Add Admin Button (if you have a create admin page) --}}
    <div class="flex justify-between items-end mb-4">
        <h1 class="text-lg font-bold">List of admin</h1>
        <flux:button
            variant="primary"
            wire:navigate
            href="{{ route('admins.admins.create') }}" {{-- Adjust this route to your admin creation page --}}
            icon="plus"
        >
            Add Admin
        </flux:button>
    </div>

    {{-- Flash Messages (if not handled globally by your layout) --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Admins List --}}
    <div class="space-y-4">
        @forelse ($admins as $admin)
            <div class="flex items-center justify-between px-4 py-2 bg-gray-50 dark:bg-gray-700 shadow-sm border border-gray-300 dark:border-gray-600 rounded-lg">
                <div class="flex items-center">
                    <div>
                        <div>
                            {{-- Check if a profile photo URL exists --}}
                            @if (!empty($admin->profile))
                                <img
                                    class="border rounded-full w-[50px] h-[50px] object-cover mr-4"
                                    src="{{ $admin->profile }}"
                                    alt="{{ $admin->name }} Avatar"
                                />
                            @else
                                {{-- Fallback: Display initials if no profile photo --}}
                                <div
                                    class="flex items-center justify-center border rounded-full w-[50px] h-[50px] mr-4 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-bold text-lg uppercase"
                                    title="{{ $admin->name }}"
                                >
                                    {{-- Calculate initials: Take the first letter of the first word and the first letter of the last word (if name has multiple words) --}}
                                    @php
                                        $nameParts = explode(' ', $admin->name);
                                        $initials = '';
                                        if (count($nameParts) > 0) {
                                            $initials .= strtoupper(substr($nameParts[0], 0, 1));
                                        }
                                        if (count($nameParts) > 1) {
                                            $initials .= strtoupper(substr(end($nameParts), 0, 1));
                                        } elseif (count($nameParts) == 1 && strlen($nameParts[0]) > 1) {
                                            // If only one word and long enough, take the first two letters
                                            $initials = strtoupper(substr($nameParts[0], 0, 2));
                                        }
                                        // Fallback if name is empty or only one very short word
                                        if (empty($initials) && !empty($admin->name)) {
                                            $initials = strtoupper(substr($admin->name, 0, 1));
                                        } elseif (empty($initials)) {
                                            $initials = '?'; // Default if name is entirely empty
                                        }
                                    @endphp
                                    {{ $initials }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div>
                        <p class="text-base font-bold text-gray-900 dark:text-gray-100">{{ $admin->name }} {{ $admin->surname }}</p>
                        <p class="text-xs text-gray-400">{{ $admin->surname }}</p>
                    </div>
                </div>
                <div class="sm:block hidden font-bold">{{ $admin->email }}</div>
                <div class="sm:block hidden font-bold">{{ $admin->phone }}</div>
                <div class="flex items-center">

                    {{-- Delete Button --}}
                    <flux:button
                        variant="danger"
                        wire:click="deleteAdmin({{ $admin->id }})"
                        wire:confirm="Are you sure to delete admin ?"
                        class="mr-2"
                    >
                        <flux:icon name="trash" class="w-5 h-5" />
                    </flux:button>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 dark:text-gray-400 p-8">
                Not admin found.
            </div>
        @endforelse
    </div>

    {{-- Pagination Links --}}
    <div class="mt-8">
        {{ $admins->links() }}
    </div>
</div>