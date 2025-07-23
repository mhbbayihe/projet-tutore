<div>
    {{-- Search Field --}}
    <form class='pb-6'>
        <div class="relative">
            <flux:input
                type="text"
                name="search"
                placeholder="Search community by name or description"
                class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                wire:model.live.debounce.500ms="search"
                icon="magnifying-glass"
                icon-position="leading"
                icon-class="text-gray-400"
                icon-size="1.5"
            />
        </div>
    </form>

    {{-- Header with "Add Community" Button --}}
    <div class="flex justify-between items-end">
        <h1 class="text-lg font-bold mb-4">Community List</h1>
        {{-- The button now navigates to the creation page, not a modal --}}
        <flux:button
            variant="primary"
            class="mb-4"
            wire:navigate {{-- Enables Livewire's client-side navigation --}}
            href="{{ route('admins.communities.create') }}" {{-- Targets the new creation route --}}
            icon="plus"
        >
            Add Community
        </flux:button>
    </div>

    {{-- Flash success/error message (if not globally managed in the layout) --}}
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed bottom-4 right-4 bg-green-500 text-white p-4 rounded-lg shadow-lg z-50">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif
    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="fixed bottom-4 right-4 bg-red-500 text-white p-4 rounded-lg shadow-lg z-50">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif


    {{-- List of Communities --}}
    <div class="space-y-4">
        @forelse ($communities as $community)
            <div class="flex items-center justify-between px-4 py-2 bg-gray-50 dark:bg-gray-700 shadow-sm border border-gray-300 dark:border-gray-600 rounded-lg">
                <div class="flex items-center">
                    <div>
                        <img
                            class="border rounded-full w-[50px] h-[50px] object-cover mr-4"
                            {{-- IMPORTANT: Use Storage::url() and the correct column name (e.g., image_path) --}}
                            src="{{ $community->image ? Storage::url($community->image) : asset('images/default_community.png') }}"
                            alt="{{ $community->name }} Avatar"
                        />
                    </div>
                    <div>
                        <p class="text-base font-bold text-gray-900 dark:text-gray-100">{{ $community->name }}</p>
                        <p class="text-xs text-gray-400">{{ number_format($community->users_count, 0, ',', ' ') }} users</p>
                    </div>
                </div>
                <div class="flex items-center">
                    {{-- View Button --}}
                    <flux:link
                        class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-600 mr-4"
                        wire:navigate
                        href="{{ route('admins.communities.view', $community->id) }}" {{-- Replace with your community detail route (e.g., route('admins.communities.show', $community)) --}}
                    >
                        <flux:icon name="eye" class="w-5 h-5" />
                    </flux:link>

                    {{-- Edit Button --}}
                    <flux:button
                        variant="second"
                        class="mr-2"
                        wire:navigate
                        href="{{ route('admins.communities.edit', $community) }}" {{-- Replace with your community edit route (e.g., route('admins.communities.edit', $community)) --}}
                    >
                        <flux:icon name="pencil-square" class="w-5 h-5" />
                    </flux:button>

                    {{-- Delete Button --}}
                    <flux:button
                        variant="danger"
                        wire:click="deleteCommunity({{ $community->id }})"
                        wire:confirm="Are you sure you want to delete this community? This action is irreversible."
                        class="mr-2"
                    >
                        <flux:icon name="trash" class="w-5 h-5" />
                    </flux:button>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 dark:text-gray-400 p-8">
                No communities found.
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $communities->links() }}
    </div>
</div>