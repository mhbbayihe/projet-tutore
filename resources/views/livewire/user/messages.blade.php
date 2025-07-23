
<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;

new #[Layout('components.layouts.user-app')] class extends Component
{
    use WithFileUploads;

}; ?>
<div class="w-full p-4 h-screen bg-gray-100 box-border px-5">
    <div class="sm:flex mt-6 sm:px-10 sm:w-[70%] sm:block hidden h-screen items-center justify-center">
        <p className="text-2xl text-gray-400 font-bold">Choose a conversation</p>
    </div>
    <div class="fixed right-2 top-0 sm:w-[25%] w-[100%] py-2 bg-white h-screen px-4 box-border">
        <livewire:user.list-message />
    </div>
</div>