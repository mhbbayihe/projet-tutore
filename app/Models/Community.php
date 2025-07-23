<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    //
    protected $fillable = [
        'name',
        'description',
        'image',
    ];

    public function users(){
        return $this->belongsToMany(User::class);
    }

    public function posts(){
        return $this->belongsToMany(Post::class);
    }

    public function getMembersCountAttribute(): int
    {
        return $this->users()->count();
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            
            return asset('storage/community_images/' . $this->image);
        }
        return '/images/default-community.png';
    }

    public function isMember(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }
}
