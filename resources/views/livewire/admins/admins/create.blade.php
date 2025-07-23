<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;

new #[Layout('components.layouts.app')] class extends Component
{
    use WithFileUploads;

    public $name = '';
    public $surname = '';
    public $email = '';
    public $phone = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255|min:2',
            'surname' => 'required|string|max:255|min:2',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|string|max:15|min:9',
        ];
    }

    public function createAdmin()
    {
        $this->validate();

        try {
            User::create([
                'name' => $this->name,
                'surname' => $this->surname,
                'email' => $this->email,
                'phone' => $this->phone,
                'role' => 'admins',
                'password' => Hash::make($this->phone),
            ]);

            return Redirect::route('admins.admins.index')->with('message', 'Admin added successfully !');

        } catch (\Exception $e) {
            session()->flash('error', 'Error : ' . $e->getMessage());
        }
    }
};
?>

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Add admin') }}
    </h2>
</x-slot>

<div class="py-6">
    <h1 class="max-w-4xl text-2xl font-bold mb-6 mx-auto sm:px-6 lg:px-8">
        Add Admin
    </h1>
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

            <form wire:submit="createAdmin">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                    <flux:input
                        type="text"
                        wire:model="name"
                        id="name"
                        class="mt-1 block w-full"
                        placeholder="Name"
                    />
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label for="surname" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Surname</label>
                    <flux:input
                        type="text"
                        wire:model="surname"
                        id="surname"
                        class="mt-1 block w-full"
                        placeholder="Surname"
                    />
                    @error('surname') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <flux:input
                        type="email"
                        wire:model="email"
                        id="email"
                        class="mt-1 block w-full"
                        placeholder="email@exemple.com" {{-- Placeholder modifié --}}
                    />
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror {{-- <<< CORRECTION ICI --}}
                </div>

                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone number</label>
                    <flux:input
                        type="text"
                        wire:model="phone"
                        id="phone"
                        class="mt-1 block w-full"
                        placeholder="Ex: +237 6xx xxx xxx"
                    />
                    @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-end mt-4">
                    <a href="{{ route('admins.admins.index') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 mr-4">
                        Back
                    </a>
                    <flux:button
                        type="submit"
                        variant="primary"
                    >
                        Add admin
                    </flux:button>
                </div>
            </form>

        </div>
    </div>
</div>