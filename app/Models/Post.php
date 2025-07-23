<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    //
    protected $fillable = [
        'text',
        'user_id',
    ];

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function postsImages(): HasMany{
        return $this->hasMany(PostsImage::class);
    }

    public function community(): BelongsToMany{
        return $this->belongsToMany(Community::class);
    }

    public function likes(): MorphMany{
        return $this->morphMany(Like::class, 'likeable');
    }

    public function isLikedByUser(?User $user = null): bool{
        if (is_null($user)) {
            $user = auth()->user();
        }
        if (!$user) {
            return false;
        }
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function favoritedBy(){
        return $this->belongsToMany(User::class, 'favorites', 'post_id', 'user_id')->withTimestamps();
    }

     public function isFavoritedByUser(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        return $this->favoritedBy->contains($user->id);
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }
    
}
