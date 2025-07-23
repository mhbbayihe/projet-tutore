<?php

namespace App\Livewire;

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

new #[Layout('components.layouts.user-app')] class extends Component
{
    public $favoritePosts;
    public array $newCommentContent = [];

    public $showFull = false;

    public function mount()
    {
        $this->loadFavoritePosts();
    }

    public function loadFavoritePosts()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to view your favorites.');
        }

        $user = Auth::user();

        $this->favoritePosts = $user->favorites()->with([
            'user',
            'postsImages',
            'likes' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            },
            'comments' => function ($query) {
                $query->with(['user', 'replies.user'])->whereNull('parent_id')->latest();
            },
        ])
        ->withCount('likes')
        ->withCount('comments')
        ->latest('favorites.created_at')
        ->get();
    }

    public function togglePostLike(int $postId)
    {
        if (!Auth::check()) {
            session()->flash('error', 'You must be logged in to like a post.');
            return;
        }

        $post = Post::find($postId);
        if (!$post) {
            session()->flash('error', 'Post not found.');
            return;
        }

        $userId = Auth::id();
        $existingLike = $post->likes()->where('user_id', $userId)->first();

        if ($existingLike) {
            $existingLike->delete();
        } else {
            $post->likes()->create(['user_id' => $userId]);
        }

        $this->loadFavoritePosts();
    }

    public function togglePostFavory(int $postId)
    {
        if (!Auth::check()) {
            session()->flash('error', 'You must be logged in to manage your favorites.');
            return;
        }

        $post = Post::find($postId);
        if (!$post) {
            session()->flash('error', 'Post not found.');
            return;
        }

        $userId = Auth::id();
        $post->favoritedBy()->toggle($userId);

        if ($post->favoritedBy->contains($userId)) {
            session()->flash('success', 'Post added to favorites!');
        } else {
            session()->flash('success', 'Post removed from favorites.');
        }

        $this->loadFavoritePosts();
    }

    public function addComment(int $postId, ?int $parentId = null)
    {
        if (!Auth::check()) {
            session()->flash('error', 'You must be logged in to comment.');
            return;
        }

        $validationKey = 'newCommentContent.' . ($parentId ?: $postId);

        $this->validate([
            $validationKey => 'required|string|min:1',
        ]);

        try {
            $commentData = [
                'user_id' => Auth::id(),
                'comment' => $this->newCommentContent[$parentId ?: $postId],
            ];

            if (is_null($parentId)) {
                $post = Post::find($postId);
                if (!$post) {
                    session()->flash('error', 'Post not found for comment.');
                    return;
                }
                $post->comments()->create($commentData);
            } else {
                $parentComment = Comment::find($parentId);
                if (!$parentComment) {
                    session()->flash('error', 'Parent comment not found.');
                    return;
                }
                $commentData['post_id'] = $parentComment->post_id;
                $commentData['parent_id'] = $parentComment->id;
                $parentComment->replies()->create($commentData);
            }

            session()->flash('success', 'Comment added successfully!');
            unset($this->newCommentContent[$parentId ?: $postId]);
            $this->loadFavoritePosts();
        } catch (\Exception $e) {
            session()->flash('error', 'Error adding comment: ' . $e->getMessage());
            \Log::error('Comment add error: ' . $e->getMessage());
        }
    }
}; ?>

<div class="relative px-5">
    <div class=" sm:px-10 sm:w-[70%] w-[100%]">
        <div class="w-full h-[200px] flex items-center justify-center bg-gray-300 mb-4">
            <h1 class="text-2xl font-bold mb-6 dark:text-white">Your Favorite Posts</h1>
        </div>

        @if (session()->has('success'))
            <div class="bg-green-100 fixed right-4 top-4 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 z-50" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="bg-red-100 fixed right-4 top-4 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 z-50" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @forelse($favoritePosts as $post)
            <div x-data="{ openModal_{{ $post->id }}: false }" class="w-full bg-white rounded-lg mb-10">
                <div class="flex justify-between items-center py-2 px-4">
                    <div class="flex items-center">
                        <a href="{{ route('users.account.view', $post->user->id) }}">
                            <div>
                                {{-- Check if a profile photo URL exists --}}
                                @if (!empty($post->user->profile)) {{-- Changed from $user->profile to $user->profile --}}
                                    <img
                                        class="border rounded-full w-[45px] h-[45px] object-cover mr-4"
                                        src="{{ Storage::url($post->user->profile) }} "
                                    />
                                @else
                                    {{-- Fallback: Display initials if no profile photo --}}
                                    <div
                                        class="flex items-center justify-center border rounded-full w-[45px] h-[45px] mr-4 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-bold text-lg uppercase"
                                    >
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
                        </a>
                        <div>
                            <p class="text-sm font-bold">{{ $post->user->name }}</p>
                            <p class="text-xs font-thin text-gray-400">{{ $post->user->surname }}</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <p class="text-sm mr-4">{{ $post->created_at->diffForHumans() }} </p><i class="mdi mdi-dots-horizontal text-2xl"></i>
                    </div>
                </div>
                @if($post->postsImages->isNotEmpty())
                    {{-- Condition pour afficher en grille si plus d'une image, sinon en une seule colonne --}}
                    <div class="relative w-full px-2 {{ $post->postsImages->count() > 1 ? 'grid grid-cols-2 gap-1' : '' }} overflow-hidden">
                        @foreach($post->postsImages as $postImage)
                            <img src="{{ Storage::url($postImage->image) }}"
                                alt="Image du post : {{ $post->content ? Str::limit($post->content, 50) : 'Pas de description' }}"
                                class="object-cover w-full h-[400px]"
                            />
                        @endforeach
                    </div>
                @endif
                <div class="px-4 py-4 text-justify">
                    <p class="text-sm">
                        {{ $showFull ? $post->text : Str::limit($post->text, 200) }}
                    </p>

                    @if (strlen($post->text) > 200)
                        <button wire:click="$toggle('showFull')" class="text-blue-500 text-sm mt-2 hover:underline">
                            {{ $showFull ? 'Read less' : 'Read more' }}
                        </button>
                    @endif
                </div>
                <div class="py-2 px-6 border-t-1 border-gray-300 pb-4 flex items-center ">
                    <button wire:click="togglePostLike({{ $post->id }})" class="flex items-center text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-500 transition focus:outline-none mr-4">
                        @if(Auth::check() && $post->isLikedByUser(Auth::user()))
                            <i class="mdi mdi-heart text-orange-600 text-2xl"></i>
                        @else
                            <i class="mdi mdi-heart-outline text-2xl"></i>
                        @endif
                        <span class="text-sm ml-1 text-orange-600 dark:text-orange-400">{{ $post->likes_count }}</span>
                    </button>
                    <button wire:click="togglePostFavory({{ $post->id }})" class="flex items-center text-gray-600 dark:text-gray-400 hover:text-yellow-500 dark:hover:text-yellow-400 transition focus:outline-none mr-4">
                        @if(Auth::check() && $post->isFavoritedByUser(Auth::user()))
                            <i class="mdi mdi-bookmark text-yellow-500 text-2xl"></i>
                        @else
                            <i class="mdi mdi-bookmark-outline text-2xl"></i>
                        @endif
                    </button>
                    <button><i class="mdi mdi-swap-horizontal text-2xl"></i></button>
                    <button @click="openModal_{{ $post->id }} = true"
                            class="flex items-center text-gray-600 ml-4 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition focus:outline-none">
                        <i class="mdi mdi-comment-outline text-2xl ml-1"></i>
                        <span class="text-sm ml-1 text-orange-600 dark:text-orange-400">{{ $post->comments_count }}</span>
                    </button>
                </div>
                {{-- Commentaires --}}
                <div x-show="openModal_{{ $post->id }}" class="div-fond fixed w-full z-60 inset-0 flex items-center  justify-center bg-black bg-opacity-50">
                    <div class="flex flex-col px-6 pb-4 w-2/3 bg-white rounded-lg max-h-8/10 box-border relative">
                        <div class="border-b-1 border-gray-300">
                            <h1 class="py-4 text-center text-2xl font-bold">Comment publication of {{ $post->user->name }}</h1>
                            <button @click="openModal_{{ $post->id }} = false" class="absolute top-0 right-6 text-2xl font-bold">
                                &times;
                            </button>
                        </div>
                        <div class="overflow-y-auto no-scrollbar">
                            @if($post->postsImages->isNotEmpty())
                                {{-- Condition pour afficher en grille si plus d'une image, sinon en une seule colonne --}}
                                <div class="relative w-full {{ $post->postsImages->count() > 1 ? 'grid grid-cols-2 gap-1' : '' }} overflow-hidden">
                                    @foreach($post->postsImages as $postImage)
                                        <img src="{{ Storage::url($postImage->image) }}"
                                            alt="Image du post : {{ $post->content ? Str::limit($post->content, 50) : 'Pas de description' }}"
                                            class="object-cover w-full h-[250px]"
                                        />
                                    @endforeach
                                </div>
                            @endif
                            <div class="px-1 text-justify py-4">
                                <p class="text-sm">{{ $post->text }}
                                </p>
                            </div>
                            <div class="bg-gray-100 rounded-lg p-4 dark:bg-gray-700 mt-6">
                                <div class="flex justify-between items-center mb-3">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Commentaires ({{ $post->comments_count }})</p>
                                </div>
                                <form wire:submit.prevent="addComment({{ $post->id }})" class="mb-4">
                                    <textarea
                                        wire:model="newCommentContent.{{ $post->id }}"
                                        class="border border-gray-300 rounded-lg w-full h-[60px] resize-none bg-white dark:bg-gray-600 dark:border-gray-500 dark:text-gray-100 focus:outline-none focus:border-orange-500 focus:ring-0 p-2 text-sm"
                                        placeholder="Écrire un commentaire..."
                                    ></textarea>
                                    @error('newCommentContent.' . $post->id) <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    <div class="flex justify-end mt-2">
                                        <button type="submit" class="flex items-center text-xs p-2 bg-orange-600 rounded-lg text-white hover:bg-orange-700 transition">
                                            Commenter <i class="mdi mdi-send-outline ml-2"></i>
                                        </button>
                                    </div>
                                </form>

                                {{-- Liste des commentaires --}}
                                @forelse($post->comments as $comment)
                                    <div class="bg-white dark:bg-gray-800 p-3 rounded-lg mb-2 shadow-sm border border-gray-200 dark:border-gray-700">
                                        <div class="flex items-start">
                                            <a href="{{ route('users.account.view', $comment->user->id) }}">
                                                <div>
                                                    {{-- Check if a profile photo URL exists --}}
                                                    @if (!empty($comment->user->profile)) {{-- Changed from $user->profile to $user->profile --}}
                                                        <img
                                                            class="border rounded-full w-[35px] h-[35px] object-cover mr-4"
                                                            src="{{ Storage::url($comment->user->profile) }} "
                                                        />
                                                    @else
                                                        {{-- Fallback: Display initials if no profile photo --}}
                                                        <div
                                                            class="flex items-center justify-center border rounded-full w-[35px] h-[35px] mr-4 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-bold text-lg uppercase"
                                                        >
                                                            @php
                                                                $nameParts = explode(' ', $comment->user->name);
                                                                $initials = '';
                                                                if (count($nameParts) > 0) {
                                                                    $initials .= strtoupper(substr($nameParts[0], 0, 1));
                                                                }
                                                                if (count($nameParts) > 1) {
                                                                    $initials .= strtoupper(substr(end($nameParts), 0, 1));
                                                                } elseif (count($nameParts) == 1 && strlen($nameParts[0]) > 1) {
                                                                    $initials = strtoupper(substr($nameParts[0], 0, 2));
                                                                }
                                                                if (empty($initials) && !empty($comment->user->name)) {
                                                                    $initials = strtoupper(substr($comment->user->name, 0, 1));
                                                                } elseif (empty($initials)) {
                                                                    $initials = '?';
                                                                }
                                                            @endphp
                                                            {{ $initials }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </a>
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $comment->user->name }}</p>
                                                    <p class="text-xs font-thin text-gray-500 dark:text-gray-400">{{ $comment->created_at->diffForHumans() }}</p>
                                                </div>
                                                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $comment->comment }}</p> {{-- <-- Utilisez $comment->comment --}}

                                                {{-- Formulaire pour répondre à un commentaire --}}
                                                <form wire:submit.prevent="addComment({{ $post->id }}, {{ $comment->id }})" class="mt-2">
                                                    <textarea
                                                        wire:model="newCommentContent.{{ $comment->id }}" {{-- Modèle dynamique pour la réponse --}}
                                                        class="border border-gray-200 rounded-lg w-full h-[40px] resize-none bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 focus:outline-none focus:border-orange-500 focus:ring-0 p-1 text-xs"
                                                        placeholder="Répondre..."
                                                    ></textarea>
                                                    @error('newCommentContent.' . $comment->id) <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                                    <div class="flex justify-end mt-1">
                                                        <button type="submit" class="flex items-center text-xs p-1 bg-blue-500 rounded-lg text-white hover:bg-blue-600 transition">
                                                            Répondre <i class="mdi mdi-reply ml-1"></i>
                                                        </button>
                                                    </div>
                                                </form>

                                                {{-- Affichage des réponses --}}
                                                @if($comment->replies->isNotEmpty())
                                                    <div class="mt-3 ml-6 border-l-2 border-gray-300 dark:border-gray-600 pl-3">
                                                        @foreach($comment->replies as $reply)
                                                            <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded-lg mb-1 shadow-sm border border-gray-100 dark:border-gray-600">
                                                                <div class="flex items-start">
                                                                    <a href="{{ route('users.account.view', $reply->user->id) }}">
                                                                        <div>
                                                                            {{-- Check if a profile photo URL exists --}}
                                                                            @if (!empty($reply->user->profile)) {{-- Changed from $user->profile to $user->profile --}}
                                                                                <img
                                                                                    class="border rounded-full w-[35px] h-[35px] object-cover mr-4"
                                                                                    src="{{ Storage::url($reply->user->profile) }} "
                                                                                />
                                                                            @else
                                                                                {{-- Fallback: Display initials if no profile photo --}}
                                                                                <div
                                                                                    class="flex items-center justify-center border rounded-full w-[35px] h-[35px] mr-4 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-bold text-lg uppercase"
                                                                                >
                                                                                    @php
                                                                                        $nameParts = explode(' ', $reply->user->name);
                                                                                        $initials = '';
                                                                                        if (count($nameParts) > 0) {
                                                                                            $initials .= strtoupper(substr($nameParts[0], 0, 1));
                                                                                        }
                                                                                        if (count($nameParts) > 1) {
                                                                                            $initials .= strtoupper(substr(end($nameParts), 0, 1));
                                                                                        } elseif (count($nameParts) == 1 && strlen($nameParts[0]) > 1) {
                                                                                            $initials = strtoupper(substr($nameParts[0], 0, 2));
                                                                                        }
                                                                                        if (empty($initials) && !empty($reply->user->name)) {
                                                                                            $initials = strtoupper(substr($reply->user->name, 0, 1));
                                                                                        } elseif (empty($initials)) {
                                                                                            $initials = '?';
                                                                                        }
                                                                                    @endphp
                                                                                    {{ $initials }}
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </a>
                                                                    <div class="flex-1">
                                                                        <p class="text-xs font-bold text-gray-900 dark:text-gray-100">{{ $reply->user->username ?? $reply->user->name }}</p>
                                                                        <p class="text-xs font-thin text-gray-500 dark:text-gray-400">{{ $reply->created_at->diffForHumans() }}</p>
                                                                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $reply->comment }}</p> {{-- <-- Utilisez $reply->comment --}}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-center text-gray-500 dark:text-gray-400 text-sm py-4">Soyez le premier à commenter !</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
        @endforelse
    </div>
    <div class="fixed right-2 top-0 w-[25%] py-2 box-border sm:block hidden">
        <livewire:user.section-home />
    </div>
</div>