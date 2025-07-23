<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ChatStarterController extends Controller
{
    //
    public function startPrivateConversation(User $user)
    {
        $currentUser = Auth::user();

        if (!$currentUser) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour démarrer une conversation.');
        }

        if ($currentUser->id === $user->id) {
            return back()->with('error', 'Vous ne pouvez pas démarrer une conversation avec vous-même.');
        }

        // Récupérer les IDs des deux utilisateurs pour la recherche symétrique
        $userId1 = $currentUser->id;
        $userId2 = $user->id;

        // Assurer un ordre canonique pour la recherche (par ex: toujours le plus petit ID d'abord)
        $participantIds = [$userId1, $userId2];
        sort($participantIds); // Trie les IDs pour que l'ordre ne pose pas de problème (ex: [8,9] ou [9,8] devient toujours [8,9])


        $conversation = Conversation::where('type', 'private')
            ->whereHas('participants', function ($query) use ($participantIds) {
                $query->whereIn('user_id', $participantIds);
            }, '=', 2) // La conversation doit avoir exactement 2 participants
            ->get() // Récupère toutes les conversations qui matchent la condition WHERE HAS
            ->filter(function ($conv) use ($participantIds) {
                // Filtrer pour s'assurer que les participants sont EXACTEMENT ces deux-là
                $convParticipantIds = $conv->participants->pluck('user_id')->sort()->values()->all();
                return $convParticipantIds == $participantIds;
            })
            ->first(); // Prend la première conversation trouvée qui correspond

        // Si pas de conversation, en créer une nouvelle
        if (!$conversation) {
            $conversation = Conversation::create(['type' => 'private']);

            $conversation->participants()->create(['user_id' => $currentUser->id]);
            $conversation->participants()->create(['user_id' => $user->id]);
        }

        return redirect()->route('chat.show', $conversation->id);
    }
}
