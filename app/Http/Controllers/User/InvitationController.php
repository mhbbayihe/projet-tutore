<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    //
    public function send(User $user, Request $request)
    {
        $sender = Auth::user();

        if ($sender->id === $user->id) {
            return back()->with('error', 'You cannot send an invitation to yourself.');
        }

        $alreadyFriends = Invitation::where(function($query) use ($sender, $user) {
                                    $query->where('sender_id', $sender->id)
                                          ->where('receiver_id', $user->id);
                                })
                                ->orWhere(function($query) use ($sender, $user) {
                                    $query->where('sender_id', $user->id)
                                          ->where('receiver_id', $sender->id);
                                })
                                ->where('status', 'accepted')
                                ->first();

        if ($alreadyFriends) {
            return back()->with('error', 'You are already friends with this user.');
        }

        $existingPendingInvitation = Invitation::where(function($query) use ($sender, $user) {
                                        $query->where('sender_id', $sender->id)
                                              ->where('receiver_id', $user->id);
                                    })
                                    ->orWhere(function($query) use ($sender, $user) {
                                        $query->where('sender_id', $user->id)
                                              ->where('receiver_id', $sender->id);
                                    })
                                    ->where('status', 'pending')
                                    ->first();

        if ($existingPendingInvitation) {
            return back()->with('error', 'An invitation is already pending between you and this user.');
        }

        try {
            Invitation::create([
                'sender_id' => $sender->id,
                'receiver_id' => $user->id,
                'status' => 'pending',
            ]);

            return back()->with('success', 'Invitation sent successfully!');
        } catch (\Exception $e) {
            Log::error('Error sending invitation: ' . $e->getMessage(), [
                'sender_id' => $sender->id,
                'receiver_id' => $user->id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return back()->with('error', 'Failed to send invitation. Please try again.');
        }
    }

    public function accept($id){
        $user = Auth::user();
        $user_id = $user->id;

        $invitation = Invitation::find($id);

        $invitation->status = 'accepted';
        $invitation->save();

        $user->friends()->attach($invitation->sender_id); // Add the sender as a friend
        $invitation->sender->friends()->attach($user_id); // Add the receiver as a friend to the sender
        return back()->with('succes', 'An invitation accepted.');
    }

}
