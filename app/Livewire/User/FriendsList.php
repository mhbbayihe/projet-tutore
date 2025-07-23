<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use App\Models\Invitation;
use Illuminate\Support\Facades\Auth;

class FriendsList extends Component
{
    public string $search = ''; // Propriété pour le terme de recherche

    public function render()
    {
        $user = Auth::user();
        $user_id = $user->id;

        // Requêtes existantes
        $list = Invitation::where('receiver_id', $user_id)->where('status', 'pending')->get();
        $userCommunities = Auth::user()->communities()->get(); // Les communautés de l'utilisateur connecté

        // Initialiser la requête pour les utilisateurs
        $usersQuery = User::whereHas('communities', function($query) use ($userCommunities){
            // Utilise whereIn sur les IDs des communautés de l'utilisateur connecté
            $query->whereIn('communities.id', $userCommunities->pluck('id'));
        })->where('id', '!=', Auth::user()->id); // Exclure l'utilisateur connecté lui-même

        // Appliquer le filtre de recherche si le champ $search n'est pas vide
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $usersQuery->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm); // Recherche par nom OU email
            });
        }

        $users = $usersQuery->get(); // Exécuter la requête et obtenir les utilisateurs filtrés

        return view('livewire.user.friends-list', [
            'communities' => $userCommunities, // Renommé pour plus de clarté
            'users' => $users,
            'lists' => $list
        ]);
    }
}
