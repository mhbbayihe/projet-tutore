<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostsImage extends Model
{
    //
    protected $table = 'posts_images';

    protected $fillable = [
        'image',
        'post_id',
    ];

    public function post(){
        return $this->belongsTo(Post::class);
    }
}
