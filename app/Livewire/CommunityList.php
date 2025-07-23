<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Community;
use Livewire\WithPagination;

class CommunityList extends Component
{
    use WithPagination;

    public $search = '';
    protected $queryString = ['search'];
    protected $listeners = [
        'communityDeleted' => '$refresh',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteCommunity($communityId)
    {
        try {
            $community = Community::findOrFail($communityId);
            if ($community->image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($community->image_path);
            }
            $community->delete();

            session()->flash('message', 'Communauté supprimée avec succès !');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression de la communauté : ' . $e->getMessage());
        }
    }

    public function render()
    {
        $communities = Community::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->withCount('users')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.community-list', [
            'communities' => $communities,
        ]);
    }
}