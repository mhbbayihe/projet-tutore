<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Friendship extends Model
{
    //
    protected $fillable = [
        'user_id',
        'friend_id'
    ];
    
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function friend(){
        return $this->belongsTo(User::class, 'friend_id');
    }

    public function scopeAreFriends($query, User $user1, User $user2)
    {
        return $query->where(function($q) use ($user1, $user2) {
            $q->where('user_id', $user1->id)
              ->where('friend_id', $user2->id);
        })->orWhere(function($q) use ($user1, $user2) {
            $q->where('user_id', $user2->id)
              ->where('friend_id', $user1->id);
        });
    }
}
