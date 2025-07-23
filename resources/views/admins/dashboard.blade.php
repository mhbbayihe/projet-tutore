<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <h1 class="text-lg font-bold py-4">Overview</h1>
        <div class="w-full flex justify-between flex-wrap">
            <div class="sm:w-[24%] w-[48%] h-35 mb-4 bg-teal-600 rounded-sm flex flex-col text-white">
                <div class="h-5/6 p-4">
                    <h1 class="font-bold text-[32px] sm:mb-3">
                        4
                    </h1>
                    <h6 class='flex'>Admin Registrations</h6>
                </div>
                <div class="w-full bg-teal-700 h-1/6 p-4 rounded-b-sm flex items-center justify-center text-sm">
                    <a class="flex" href="">See more <Icon class="ml-2" path={mdiArrowRightCircle} size={0.8} /></a>
                </div>
            </div>

            <div class="sm:w-[24%] w-[48%] h-35 mb-4 bg-green-600 rounded-sm flex flex-col text-white">
                <div class="h-5/6 p-4">
                    <h1 class="font-bold text-[32px] sm:mb-3">
                        16
                    </h1>
                    <h6 class='flex'>User Community</h6>
                </div>
                <div class="w-full bg-green-700 h-1/6 p-4 rounded-b-sm flex items-center justify-center text-sm">
                    <a class="flex" href="">See more <Icon class="ml-2" path={mdiArrowRightCircle} size={0.8} /></a>
                </div>
            </div>

            <div class="sm:w-[24%] w-[48%] h-35 bg-yellow-500 rounded-sm flex flex-col text-black">
                <div class="h-5/6 p-4">
                    <h1 class="font-bold text-[32px] sm:mb-3">
                        16
                    </h1>
                    <h6 class='flex'>Total Users</h6>
                </div>
                <div class="w-full bg-yellow-600 h-1/6 p-4 rounded-b-sm flex items-center justify-center text-sm">
                    <a class="flex" href="">See more <Icon class="ml-2" path={mdiArrowRightCircle} size={0.8} /></a>
                </div>
            </div>

            <div class="sm:w-[24%] w-[48%] h-35 bg-red-600 rounded-sm flex flex-col text-white">
                <div class="h-5/6 p-4">
                    <h1 class="font-bold text-[32px] sm:mb-3">
                        8
                    </h1>
                    <h6 class='flex'>Posts</h6>
                </div>
                <div class="w-full bg-red-700 h-1/6 p-4 rounded-b-sm flex items-center justify-center text-sm">
                    <a class="flex" href="">See more <Icon class="ml-2" path={mdiArrowRightCircle} size={0.8} /></a>
                </div>
            </div>
        </div>
        <div class="sm:flex block justify-between">
            <div class="sm:w-9/20 w-full mb-4">
                @livewire('community-user-chart')
            </div>
            <div class="sm:w-9/20 w-full mb-4">
                @livewire('user-chart')
            </div>
        </div>
        @livewire('last-user')
    </div>
</x-layouts.app>
