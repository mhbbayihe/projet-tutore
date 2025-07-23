<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use App\Models\Invitation;
use Illuminate\Support\Facades\Auth;

class Sidebar extends Component
{
    public string $search = ''; // Property for the search term

    public function render()
    {
        $currentUser = Auth::user();
        $currentUserId = $currentUser->id;

        // Fetch pending invitations where the current user is the receiver
        $pendingInvitations = Invitation::where('receiver_id', $currentUserId)
                                        ->where('status', 'pending')
                                        ->with('sender') // Eager load sender for display
                                        ->get();

        // Query for accepted friends
        $friendsQuery = User::where(function($query) use ($currentUserId) {
            // Users who sent an accepted invitation TO the current user
            $query->whereHas('sentInvitations', function($q) use ($currentUserId) {
                $q->where('receiver_id', $currentUserId)
                  ->where('status', 'accepted');
            })
            // OR users who received an accepted invitation FROM the current user
            ->orWhereHas('receivedInvitations', function($q) use ($currentUserId) {
                $q->where('sender_id', $currentUserId)
                  ->where('status', 'accepted');
            });
        })
        ->where('id', '!=', $currentUserId); // Exclude the current user from the list

        // Apply search filter if the $search property is not empty
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $friendsQuery->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm); // Search by name OR email
            });
        }

        $friendsList = $friendsQuery->get(); // Execute the query to get friends

        // Fetch user's communities (assuming this is needed for the Communities dropdown)
        $userCommunities = $currentUser->communities()->get();

        return view('livewire.user.sidebar', [
            'communities' => $userCommunities,
            'users' => $friendsList, // Pass the friends list to the 'users' variable
            'lists' => $pendingInvitations,
            'currentID' => $currentUserId
        ]);
    }
}