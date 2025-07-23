<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Community;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage; // Added for potential image deletion cleanup if needed later

new #[Layout('components.layouts.app')] class extends Component
{
    use WithFileUploads;

    public $name = '';
    public $description = '';
    public $image = null; // Represents the uploaded file

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:communities,name',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:1024', // Max 1MB file size
        ];
    }

    public function createCommunity() // Renamed for clarity: saveCommunity to createCommunity
    {
        $this->validate();

        try {
            $imagePath = null;
            if ($this->image) {
                // Store the image in the 'images/communities' directory within the 'public' disk
                $imagePath = $this->image->store('images/communities', 'public');
            }

            Community::create([
                'name' => $this->name,
                'description' => $this->description,
                'image' => $imagePath, // Ensure your Community model has 'image_path' fillable
            ]);

            // Redirect to the communities list page with a success message
            return Redirect::route('admins.communities.index')->with('message', 'Community added successfully!');

        } catch (\Exception $e) {
            // Flash an error message if something goes wrong
            session()->flash('error', 'Error adding community: ' . $e->getMessage());
        }
    }
};
?>

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Add New Community') }}
    </h2>
</x-slot>

<div class="py-6">
    <h1 class="max-w-4xl text-2xl font-bold mb-6 mx-auto sm:px-6 lg:px-8">
        Add Community
    </h1>
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

            <form wire:submit="createCommunity">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Community Name</label>
                    <flux:input
                        type="text"
                        wire:model="name"
                        id="name"
                        class="mt-1 block w-full"
                        placeholder="Community Name"
                    />
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <textarea
                        wire:model="description"
                        id="description"
                        rows="3"
                        class="mt-1 p-2 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        placeholder="Describe the community..."
                    ></textarea>
                    @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Community Image</label>
                    <input type="file" wire:model="image" id="image" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-violet-50 file:text-violet-700
                        hover:file:bg-violet-100"
                    />
                    @error('image') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @if ($image)
                        <img src="{{ $image->temporaryUrl() }}" class="mt-2 h-20 w-20 object-cover rounded-full" alt="Image preview">
                    @endif
                </div>

                <div class="flex items-center justify-end mt-4">
                    <a href="{{ route('admins.communities.index') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 mr-4">
                        Back
                    </a>
                    <flux:button
                        type="submit"
                        variant="primary"
                    >
                        Add Community
                    </flux:button>
                </div>
            </form>

        </div>
    </div>
</div>