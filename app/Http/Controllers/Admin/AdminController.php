<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Models\User;
use App\Models\Report;
use App\Models\Community;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Notifications\NewReportNotification;

class AdminController extends Controller
{
    //
public function destroy(Community $community, Post $post)
{
    // Assure-toi que la relation est chargée
    $post->load('community');

    // Vérifie que ce post appartient bien à cette communauté
    if (!$post->communities->contains($community->id)) {
        abort(403, 'Unauthorized');
    }

    // Supprime les images
    foreach ($post->postsImages as $image) {
        Storage::delete($image->image);
        $image->delete();
    }

    // Supprime les relations
    $post->comments()->delete();
    $post->likes()->delete();
    $post->favorites()->detach();
    $post->communities()->detach(); // détache les liens avec les communautés

    $post->delete();

    return redirect()->route('home')->with('success', 'Post deleted successfully.');
}

}
