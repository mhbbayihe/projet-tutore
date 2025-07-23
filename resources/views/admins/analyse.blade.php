<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <h1 class="text-lg font-bold py-4">Analyse Part</h1>
        <div class="sm:flex block justify-between">
           <div class="sm:w-2/3 w-full">
                <div class="mb-6">
                    @livewire('community-user-chart')
                </div>
                <div class="mb-6">
                    @livewire('user-chart')
                </div>
            </div>
            <div class="sm:w-2/7 w-1/2">
                @livewire('gender-user')
            </div>     
        </div>
    </div>
</x-layouts.app>
