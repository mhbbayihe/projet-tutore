<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    //
    protected $fillable = [
        'user_id',
        'reportable_type',
        'reportable_id',
        'reason',
        'status',
    ];

    /**
     * Get the user that made the report.
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reportable model (e.g., Post, Comment, User).
     */
    public function reportable(){
        return $this->morphTo();
    }
}
