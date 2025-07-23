<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Community;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule; // Added for unique rule when updating
use Illuminate\Support\Facades\Storage; // For deleting old images

new #[Layout('components.layouts.app')] class extends Component
{
    use WithFileUploads;

    public Community $community; // Public property for the bound Community model
    public $name = '';
    public $description = '';
    public $newImage = null; // For the new uploaded image
    public $existingImage = null; // To display the current image path/URL
    public $deleteExistingImage = false; // Checkbox to remove the image

    // Mount method to load the community data when the component is initialized
    public function mount(Community $community): void
    {
        $this->community = $community;
        $this->name = $community->name;
        $this->description = $community->description;
        $this->existingImage = $community->image_path; // Assuming 'image_path' column
    }

    protected function rules(): array
    {
        return [
            // Ensure the name is unique, but ignore the current community's name
            'name' => ['required', 'string', 'max:255', Rule::unique('communities')->ignore($this->community->id)],
            'description' => 'nullable|string|max:1000',
            'newImage' => 'nullable|image|max:1024', // Max 1MB for new image
            'deleteExistingImage' => 'boolean', // Validation for the checkbox
        ];
    }

    public function updateCommunity()
    {
        $this->validate();

        try {
            $imagePath = $this->existingImage; // Default to existing image path

            // Case 1: A new image is uploaded
            if ($this->newImage) {
                // Delete old image if it exists
                if ($this->existingImage) {
                    Storage::disk('public')->delete($this->existingImage);
                }
                $imagePath = $this->newImage->store('images/communities', 'public');
            }
            // Case 2: User checked "delete image" AND no new image was uploaded
            elseif ($this->deleteExistingImage) {
                if ($this->existingImage) {
                    Storage::disk('public')->delete($this->existingImage);
                }
                $imagePath = null; // Set image path to null in DB
            }
            // Case 3: No new image, and delete checkbox not checked (keep existing image)
            // $imagePath remains $this->existingImage (its initial value)

            $this->community->update([
                'name' => $this->name,
                'description' => $this->description,
                'image' => $imagePath, // Use the determined image path
            ]);

            return Redirect::route('admins.communities.index')->with('message', 'Community updated successfully!');

        } catch (\Exception $e) {
            session()->flash('error', 'Error updating community: ' . $e->getMessage());
        }
    }
};
?>

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Edit Community') }}
    </h2>
</x-slot>

<div class="py-6">
    <h1 class="max-w-4xl text-2xl font-bold mb-6 mx-auto sm:px-6 lg:px-8">
        Edit Community: {{ $community->name }}
    </h1>
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

            <form wire:submit="updateCommunity">
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
                    <label for="newImage" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Community Image (optional)</label>
                    <input type="file" wire:model="newImage" id="newImage" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-violet-50 file:text-violet-700
                        hover:file:bg-violet-100"
                    />
                    @error('newImage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                    @if ($newImage)
                        <p class="mt-2 text-sm text-gray-500">New image preview:</p>
                        <img src="{{ $newImage->temporaryUrl() }}" class="mt-2 h-20 w-20 object-cover rounded-full" alt="New Image Preview">
                    @elseif ($existingImage)
                        <p class="mt-2 text-sm text-gray-500">Current image:</p>
                        <img src="{{ Storage::url($existingImage) }}" class="mt-2 h-20 w-20 object-cover rounded-full" alt="Current Image">
                        <div class="mt-2 flex items-center">
                            <input type="checkbox" wire:model.live="deleteExistingImage" id="deleteExistingImage" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <label for="deleteExistingImage" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Remove current image</label>
                        </div>
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
                        Update Community
                    </flux:button>
                </div>
            </form>

        </div>
    </div>
</div>