<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'phone',
        'password',
        'role',
        'provider',
        'provider_id',
        'profile',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function communities(){
        return $this->belongsToMany(Community::class);
    }

    public function likes(){
        return $this->hasMany(Like::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function conversationParticipants()
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function conversations()
    {
        return $this->hasManyThrough(Conversation::class, ConversationParticipant::class, 'user_id', 'id', 'id', 'conversation_id');
    }

    public function sentInvitations(){
        return $this->hasMany(Invitation::class, 'sender_id');
    }

    public function receivedInvitations(){
        return $this->hasMany(Invitation::class, 'receiver_id');
    }

    public function friendships(){
        return $this->hasMany(Friendship::class, 'user_id');
    }

    public function friends(){
        return $this->belongsToMany(User::class, 'friendships','user_id', 'friend_id');
    }

    public function detailsusers(){
        return $this->hasOne(DetailsUser::class);
    }

    public function schoolsusers(){
        return $this->hasMany(SchoolsUser::class);
    }

    public function post(){
        return $this->hasMany(Post::class);
    }

    public function favorites() {
        return $this->belongsToMany(Post::class, 'favorites', 'user_id', 'post_id')->withTimestamps();
    }

    public function reports(){
        return $this->morphMany(Report::class, 'reportable');
    }

    public function reportedItems(){
        return $this->hasMany(Report::class, 'user_id');
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        return $this->role === $roles;
    }
}
