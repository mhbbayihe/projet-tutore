<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use App\Models\Invitation;
use Illuminate\Support\Facades\Auth;

class SectionHome extends Component
{
    public string $search = ''; // Propriété pour le terme de recherche

    public function render()
    {
        $user = Auth::user();
        $user_id = $user->id;

        $list = Invitation::where('receiver_id', $user_id)->where('status', 'pending')->get();
        $userCommunities = Auth::user()->communities()->get();

        // Initialiser la requête pour les utilisateurs
        $usersQuery = User::whereHas('communities', function($query) use ($userCommunities){
            $query->whereIn('communities.id', $userCommunities->pluck('id'));
        })->where('id', '!=', Auth::user()->id);

        // Appliquer le filtre de recherche si le champ $search n'est pas vide
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $usersQuery->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm);
            });
        }

        $users = $usersQuery->get();

        return view('livewire.user.section-home', [
            'communities' => $userCommunities,
            'users' => $users,
            'lists' => $list
        ]);
    }
}