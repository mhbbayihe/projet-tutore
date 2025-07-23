<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\Friendship; // Assurez-vous d'importer votre modèle Friendship
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule; // Garder si nécessaire, sinon peut être retiré
use Livewire\WithFileUploads; // Garder si nécessaire, sinon peut être retiré

class ListMessage extends Component
{
    public string $search = '';

    public $friendships; // Changer de $users à $friendships pour plus de clarté

    public function mount()
    {
        $this->loadFriendships();
    }

    public function loadFriendships()
    {
        // Charger les amitiés où l'utilisateur connecté est le 'user_id'
        $query = Friendship::where('user_id', Auth::id());

        // Eager load the 'friend' (the other user in the friendship)
        $query->with('friend');

        // Appliquer le filtre de recherche sur le nom de l'ami
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $query->whereHas('friend', function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm); // Optionnel: rechercher aussi par email de l'ami
            });
        }

        $this->friendships = $query->get();
    }

    public function updatedSearch()
    {
        $this->loadFriendships();
    }

    public function render()
    {
        // Passer $this->friendships à la vue
        return view('livewire.user.list-message', ['friendships' => $this->friendships]);
    }
}