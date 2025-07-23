<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailsUser extends Model
{
    //
    protected $table = 'details_user';

    protected $fillable = [
        'github_link',
        'website_link',
        'linkedin_link',
        'country',
        'city',
        'gender',
        'residence',
        'birthday',
        'biography'
    ];

    protected $casts = [
        'birthday' => 'date',
    ];

    public function users(){
        return $this->hasOne(User::class);
    }
}
