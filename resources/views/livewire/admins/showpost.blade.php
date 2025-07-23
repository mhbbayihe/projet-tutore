<?php

namespace App\Livewire;

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Notifications\UserWarned; // Importez la classe de notification

// La classe PHP du composant Livewire Volt
new #[Layout('components.layouts.app')] class extends Component
{
    use WithFileUploads;

    public Post $post;
    public string $newCommentContent = '';
    public bool $showFullText = false;
    public ?int $replyingToCommentId = null;

    public bool $isOpen = false; // Ces propriétés ne sont pas utilisées dans ShowPost, mais laissées pour référence si besoin
    public array $images = [];
    public array $imagePreviewUrls = [];
    public ?string $message = '';


    public function mount(Post $post)
    {
        $this->post = $post->load([
            'user',
            'postsImages',
            'likes' => function ($query) {
                if (Auth::check()) {
                    $query->where('user_id', Auth::id());
                }
            },
            'favoritedBy' => function ($query) { // Revert to 'favoritedBy' if that's the correct relationship name
                if (Auth::check()) {
                    $query->where('user_id', Auth::id());
                }
            },
            'comments' => function ($query) {
                $query->with(['user', 'replies.user'])
                      ->whereNull('parent_id')
                      ->latest();
            },
        ])->loadCount('likes', 'comments');
    }

    public function togglePostLike()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Vous devez être connecté pour aimer une publication.');
            return;
        }

        $userId = Auth::id();
        $existingLike = $this->post->likes()->where('user_id', $userId)->first();

        if ($existingLike) {
            $existingLike->delete();
            session()->flash('success', 'Publication non aimée avec succès.');
        } else {
            $this->post->likes()->create(['user_id' => $userId]);
            session()->flash('success', 'Publication aimée avec succès !');
        }

        $this->post->loadCount('likes');
        $this->post->load(['likes' => fn($q) => $q->where('user_id', Auth::id())]);
    }

    public function togglePostFavory()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Vous devez être connecté pour ajouter aux favoris.');
            return;
        }

        $userId = Auth::id();
        $this->post->favoritedBy()->toggle($userId);

        if ($this->post->favoritedBy->contains($userId)) {
            session()->flash('success', 'Publication ajoutée aux favoris !');
        } else {
            session()->flash('success', 'Publication retirée des favoris.');
        }

        $this->post->load(['favoritedBy' => fn($q) => $q->where('user_id', Auth::id())]);
    }

    public function addComment(?int $parentId = null)
    {
        if (!Auth::check()) {
            session()->flash('error', 'Vous devez être connecté pour commenter.');
            return;
        }

        $this->validate([
            'newCommentContent' => 'required|string|min:1|max:500',
        ]);

        try {
            $commentData = [
                'user_id' => Auth::id(),
                'comment' => $this->newCommentContent,
                'post_id' => $this->post->id,
            ];

            if ($parentId) {
                $parentComment = Comment::find($parentId);
                if (!$parentComment) {
                    session()->flash('error', 'Commentaire parent introuvable.');
                    return;
                }
                $commentData['parent_id'] = $parentId;
                $parentComment->replies()->create($commentData);
            } else {
                $this->post->comments()->create($commentData);
            }

            session()->flash('success', 'Commentaire ajouté avec succès !');
            $this->reset('newCommentContent');
            $this->replyingToCommentId = null;
            
            $this->post->load([
                'comments' => function ($query) {
                    $query->with(['user', 'replies.user'])
                          ->whereNull('parent_id')
                          ->latest();
                },
            ])->loadCount('comments');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'ajout du commentaire : ' . $e->getMessage());
            \Log::error('Comment error: ' . $e->getMessage());
        }
    }

    public function setReplyingTo(int $commentId)
    {
        $this->replyingToCommentId = $commentId;
        $this->newCommentContent = '';
    }

    public function cancelReply()
    {
        $this->replyingToCommentId = null;
        $this->newCommentContent = '';
    }

    public function rules(): array
    {
        return [
            'newCommentContent' => ['required', 'string', 'min:1', 'max:500'],
        ];
    }

    public function warnUser()
    {
        if (!Auth::check() || !Auth::user()->hasRole('super-admin')) {
            session()->flash('error', 'Accès non autorisé.');
            return;
        }

        $this->post->user->notify(new UserWarned($this->post, "Votre publication contient du contenu inapproprié."));

        session()->flash('info', 'L\'utilisateur ' . $this->post->user->name . ' a été averti.');
        // Logique réelle d'avertissement ici (par exemple, enregistrer dans une table d'avertissements)
    }

    public function deletePost()
{
    if (!Auth::check()) {
        session()->flash('error', 'You must be logged in to delete a post.');
        return;
    }

    // Check authorization (optional)
    if (Auth::id() !== $this->post->user_id && !Auth::user()->hasRole('super-admin')) {
        session()->flash('error', 'You are not authorized to delete this post.');
        return;
    }

    try {
        // Delete images
        foreach ($this->post->postsImages as $image) {
            Storage::delete($image->image);
            $image->delete();
        }

        // Delete related data
        $this->post->comments()->delete();
        $this->post->likes()->delete();
        $this->post->favoritedBy()->detach();

        // Delete the post
        $this->post->delete();

        session()->flash('info', 'Post deleted successfully.');

        return redirect()->route('dashboard'); // Or any other page
    } catch (\Exception $e) {
        \Log::error('Error deleting post: ' . $e->getMessage());
        session()->flash('error', 'Failed to delete post.');
    }
}



}; ?>

{{-- Le frontend (Blade) du composant commence ici --}}
<div>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            {{-- Section du Post --}}
            <div class="mb-6">
                @if (session()->has('info'))
                    <div class="bg-green-100 fixed right-4 top-4 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        {{ session('info') }}
                    </div>
                @endif
                <div class="flex items-center mb-4">
                    {{-- Avatar de l'utilisateur --}}
                    <div>
                        @if (!empty($post->user->profile))
                            <img class="border rounded-full w-[45px] h-[45px] object-cover mr-4"
                                 src="{{ Storage::url($post->user->profile) }}"
                                 alt="{{ $post->user->name }}'s profile image" />
                        @else
                            <div class="flex items-center justify-center border rounded-full w-[45px] h-[45px] mr-4 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-bold text-lg uppercase">
                                @php
                                    $nameParts = explode(' ', $post->user->name);
                                    $initials = '';
                                    if (count($nameParts) > 0) {
                                        $initials .= strtoupper(substr($nameParts[0], 0, 1));
                                    }
                                    if (count($nameParts) > 1) {
                                        $initials .= strtoupper(substr(end($nameParts), 0, 1));
                                    } elseif (count($nameParts) == 1 && strlen($nameParts[0]) > 1) {
                                        $initials = strtoupper(substr($nameParts[0], 0, 2));
                                    }
                                    if (empty($initials) && !empty($post->user->name)) {
                                        $initials = strtoupper(substr($post->user->name, 0, 1));
                                    } elseif (empty($initials)) {
                                        $initials = '?';
                                    }
                                @endphp
                                {{ $initials }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $post->user->name }} {{ $post->user->surname }}</p>
                        <p class="text-xs font-thin text-gray-400">{{ $post->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                {{-- Contenu du Post --}}
                <p class="text-sm text-gray-800 dark:text-gray-200 mb-4">
                    <span x-data="{ showFullText: @entangle('showFullText') }" class="block">
                        <span x-show="!showFullText">{{ Str::limit($post->text, 200) }}</span>
                        <span x-show="showFullText">{{ $post->text }}</span>
                        @if (strlen($post->text) > 200)
                            <button @click="showFullText = !showFullText" class="text-blue-500 text-sm mt-2 hover:underline">
                                <span x-show="!showFullText">Read more</span>
                                <span x-show="showFullText">Read less</span>
                            </button>
                        @endif
                    </span>
                </p>

                {{-- Images du Post --}}
                @if($post->postsImages->isNotEmpty())
                    <div class="relative w-full {{ $post->postsImages->count() > 1 ? 'grid grid-cols-2 gap-1' : '' }} overflow-hidden rounded-lg mb-4">
                        @foreach($post->postsImages as $postImage)
                            <img src="{{ Storage::url($postImage->image) }}"
                                alt="Post image: {{ $post->text ? Str::limit($post->text, 50) : 'No description' }}"
                                class="object-cover w-full h-[300px] sm:h-[400px]" />
                        @endforeach
                    </div>
                @endif

                {{-- Actions sur le Post (Likes, Favoris, Commentaires) --}}
                <div class="flex items-center justify-between py-2 border-t border-b border-gray-200 dark:border-gray-700">
                    <button wire:click="togglePostLike" class="flex items-center text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-500 transition focus:outline-none mr-4">
                        @if(Auth::check() && $post->likes->isNotEmpty())
                            <i class="mdi mdi-heart text-orange-600 text-2xl"></i>
                        @else
                            <i class="mdi mdi-heart-outline text-2xl"></i>
                        @endif
                        <span class="text-sm ml-1 text-orange-600 dark:text-orange-400">{{ $post->likes_count }}</span>
                    </button>
                    <button wire:click="togglePostFavory" class="flex items-center text-gray-600 dark:text-gray-400 hover:text-yellow-500 dark:hover:text-yellow-400 transition focus:outline-none mr-4">
                        @if(Auth::check() && $post->isFavoritedByUser(Auth::user())) {{-- Updated to use isFavoritedByUser method --}}
                            <i class="mdi mdi-bookmark text-yellow-500 text-2xl"></i>
                        @else
                            <i class="mdi mdi-bookmark-outline text-2xl"></i>
                        @endif
                    </button>
                    <button class="text-gray-600 dark:text-gray-400"><i class="mdi mdi-swap-horizontal text-2xl"></i></button>

                    <button class="flex items-center text-gray-600 dark:text-gray-400 focus:outline-none">
                        <i class="mdi mdi-comment-outline text-2xl ml-1"></i>
                        <span class="text-sm ml-1 text-orange-600 dark:text-orange-400">{{ $post->comments_count }}</span>
                    </button>
                </div>
                {{-- Boutons d'administration --}}
                <div class="flex items-center ml-4 space-x-2">
                    <button wire:click="warnUser" class="px-3 py-1 text-sm bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition">
                        Warn User
                    </button>
                    <button wire:click="deletePost" type="button" class="px-3 py-1 text-sm bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                        Delete Post
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
