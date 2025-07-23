<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolsUser extends Model
{
    //
    protected $table = 'schools_user';

    protected $fillable = [
        'name',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
