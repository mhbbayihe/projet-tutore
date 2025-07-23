<?php

namespace App\Livewire\User;

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Livewire\Attributes\Url;
use App\Models\Community;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

new #[Layout('components.layouts.user-app')] class extends Component
{
    use WithFileUploads;

    public Collection $communities;
    
    #[Url(as: 'q')]
    public string $search = '';

    public function mount()
    {
        $this->loadCommunities();
    }

    public function loadCommunities()
    {
        $query = Community::query();

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        // Charge les communautés et la relation 'users' (pour les membres)
        // puis utilise l'accesseur 'members_count' dans la vue.
        $this->communities = $query->with('users')->orderBy('name')->get(); 
    }

    public function updatedSearch()
    {
        $this->loadCommunities();
    }
}; ?>

<div class="relative px-5">
    <div class="mt-6 sm:px-10 sm:w-[70%] w-[100%]">
        <h1 class="text-lg font-bold mb-4">Liste des communautés</h1>
        <form wire:submit.prevent="loadCommunities" class='pb-6'>
            <div class="relative">
                <input 
                    wire:model.live.debounce.300ms="search" 
                    class="border-1 w-full rounded-full h-[40px] pl-10 p-2 border-gray-300 focus:ring-blue-500 focus:border-blue-500" 
                    type="search" 
                    placeholder="Trouver une communauté" 
                />
                <i class="mdi mdi-magnify absolute top-1/2 left-3 -translate-y-1/2 text-2xl text-gray-400"></i>
            </div>
        </form>
        <div>
            @forelse ($communities as $community)
                <div class="flex items-center justify-between px-4 py-2 bg-gray-50 shadow-sm border-1 border-gray-300 rounded-lg mb-4">
                    <div class="flex items-center">
                        <div>
                            <img
                                class="border rounded-full mr-4 w-[45px] h-[45px] object-cover"
                                src="{{ Storage::url($community->image )}}" {{-- Utilise l'accesseur image_url du modèle Community --}}
                                alt="{{ $community->name }} logo"
                            />
                        </div>
                        <div>
                            <p class="text-base font-bold">{{ $community->name }}</p>
                            <p class="text-xs text-gray-400">{{ $community->members_count }} utilisateurs</p> {{-- Utilise l'accesseur members_count --}}
                        </div>
                    </div>
                    <div>
                        <a class="text-sm text-blue-600 hover:text-blue-700" href="{{ route('community.show', $community->id) }}">
                            <i class="mdi mdi-eye text-xl"></i>
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500">Aucune communauté trouvée.</p>
            @endforelse
        </div>
    </div>
    <div class="fixed right-2 top-0 w-[25%] py-2 box-border sm:block hidden">
        <livewire:user.section-home />
    </div>
</div>