<?php

namespace App\Livewire; // Adjust this namespace if it's different for your Volt components

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Post;
use App\Models\Like;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use App\Models\Community;
use App\Models\PostsImage;
use App\Models\Favorite;

new #[Layout('components.layouts.user-app')] class extends Component
{
    use WithFileUploads;

    public bool $isOpen = false;

    public $images = [];
    public array $imagePreviewUrls = [];

    public ?string $message = '';
    public string $newPostContent = '';

    public Community $community;
    public array $newCommentContent = [];

    public $posts;
    public $showFull = false;

    public function mount(Community $community)
    {
        $this->community = $community;
        $this->loadPosts();
    }

    public function loadPosts()
    {
        $this->posts = $this->community->posts()->with([
            'user',
            'postsImages',
            'likes' => function ($query) {
                if (Auth::check()) {
                    $query->where('user_id', Auth::id());
                }
            },
            'favoritedBy' => function ($query) {
                if (Auth::check()) {
                    $query->where('user_id', Auth::id());
                }
            },                    
            'comments' => function ($query) {
                $query->with(['user', 'replies.user'])
                        ->whereNull('parent_id')
                        ->latest();
            },
        ])->withCount('likes')->withCount('comments')->latest()->get();

        foreach ($this->posts as $post) {
            $this->newCommentContent[$post->id] = '';
        }
    }

    public function togglePostLike(int $postId)
    {
        if (!Auth::check()) {
            session()->flash('error', 'Vous devez être connecté pour liker un post.');
            return;
        }

        $post = Post::find($postId);
        if (!$post) {
            session()->flash('error', 'Post non trouvé.');
            return;
        }

        $userId = Auth::id();
        $existingLike = $post->likes()->where('user_id', $userId)->first();

        if ($existingLike) {
            $existingLike->delete();
            session()->flash('success', 'Like du post supprimé.');
        } else {
            $post->likes()->create(['user_id' => $userId]);
            session()->flash('success', 'Post liké !');
        }

        $this->loadPosts();
    }

    public function togglePostFavory(int $postId)
    {
        if (!Auth::check()) {
            session()->flash('error', 'you must be connected.');
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
            session()->flash('success', 'Favory post added !');
        } else {
            session()->flash('success', 'Favory post deleted.');
        }

        $this->loadPosts();
    }

    public function rules(): array
    {
        return [
            'newPostContent' => ['nullable', 'string', 'min:5', 'max:2000'],
            'message' => ['nullable', 'string', 'min:5', 'max:2000'],
            'images.*' => ['nullable', 'image', 'max:2048', 'mimes:jpg,png,jpeg,gif,svg'],
            'images' => ['array', 'max:5'],
            'newCommentContent.*' => ['nullable', 'string', 'min:1', 'max:500']
        ];
    }

    public function updatedImages()
    {
        $this->resetErrorBag('images.*'); // Clear errors for individual images
        $this->resetErrorBag('images'); // Clear errors for the images array

        $this->imagePreviewUrls = [];
        if ($this->images) {
            foreach ($this->images as $index => $image) {
                try {
                    // Validate each image individually
                    $this->validateOnly("images.{$index}");
                    $this->imagePreviewUrls[] = $image->temporaryUrl();
                } catch (\Illuminate\Validation\ValidationException $e) {
                    // Validation errors are automatically handled by Livewire.
                    // You might want to log this or add specific UI feedback if needed.
                }
            }
        }
    }

    public function savePost()
    {
        // Validate for the modal form (message and images)
        $this->validate([
            'message' => ['required_without:images', 'string', 'min:5', 'max:2000'], // Message is required if no images
            'images' => ['required_without:message', 'array', 'max:5'], // Images are required if no message
            'images.*' => ['nullable', 'image', 'max:2048', 'mimes:jpg,png,jpeg,gif,svg'],
        ]);

        if (!Auth::check()) {
            session()->flash('error', 'Vous devez être connecté pour publier.');
            return;
        }

        if (!$this->community || !$this->community->id) {
            session()->flash('error', 'La communauté n\'a pas pu être identifiée pour la publication.');
            return;
        }

        try {
            $post = Post::create([
                'text' => $this->message, // 'content' should match your DB column
                'user_id' => Auth::id(),
                'community_id' => $this->community->id, // Directly assign community_id
            ]);

            $post->community()->attach($this->community->id);

            if (!empty($this->images)) {
                foreach ($this->images as $image) {
                    $path = $image->store('posts_photos', 'public'); // Store in 'posts_photos'
                    PostsImage::create([
                        'post_id' => $post->id,
                        'image' => $path,
                    ]);
                }
            }

            session()->flash('success', 'Publication créée avec succès !');



            $this->reset(['isOpen', 'images', 'message', 'imagePreviewUrls']);
            $this->loadPosts(); // Reload posts after creation

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions to show errors in the UI
            throw $e;
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la création de la publication : ' . $e->getMessage());
            \Log::error('Erreur de création de publication : ' . $e->getMessage() . ' Trace : ' . $e->getTraceAsString());
        }
    }

    public function createMainPost()
    {
        // Validation for the main post textarea
        $this->validate([
            'newPostContent' => 'required|string|min:5|max:2000',
        ]);

        if (!Auth::check()) {
            session()->flash('error', 'Vous devez être connecté pour publier un post.');
            return;
        }

        try {
            Post::create([
                'user_id' => Auth::id(),
                'content' => $this->newPostContent,
                'community_id' => $this->community->id,
            ]);

            session()->flash('success', 'Post principal créé avec succès !');
            $this->reset('newPostContent');
            $this->loadPosts(); // Reload posts after creating main post
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la création du post principal : ' . $e->getMessage());
            \Log::error('Erreur de création de post principal : ' . $e->getMessage());
        }
    }

    public function addComment(int $postId, ?int $parentId = null)
    {
        if (!Auth::check()) {
            session()->flash('error', 'Vous devez être connecté pour commenter.');
            return;
        }

        $validationKey = 'newCommentContent.' . ($parentId ?: $postId);

        $this->validate([
            $validationKey => 'required|string|min:1|max:500',
        ]);

        if (is_null($parentId)) {
            $parent = Post::find($postId);
            if (!$parent) {
                session()->flash('error', 'Post non trouvé pour le commentaire.');
                return;
            }
        } else { // Si c'est une réponse, on trouve le commentaire parent
            $parent = Comment::find($parentId);
            if (!$parent) {
                session()->flash('error', 'Commentaire parent non trouvé.');
                return;
            }
        }

        try {
            $commentData = [
                'user_id' => Auth::id(),
                'comment' => $this->newCommentContent[$parentId ?: $postId],
            ];

            if (is_null($parentId)) {
                $commentData['post_id'] = $parent->id;
                $parent->comments()->create($commentData);
            } else {
                $commentData['post_id'] = $parent->post_id;
                $commentData['parent_id'] = $parent->id;
                $parent->replies()->create($commentData);
            }

            session()->flash('success', 'Commentaire ajouté avec succès !');
            unset($this->newCommentContent[$parentId ?: $postId]);
            $this->loadPosts();
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'ajout du commentaire : ' . $e->getMessage());
            \Log::error('Erreur ajout commentaire : ' . $e->getMessage());
        }
    }

}; ?>

<div class="relative px-5">
    <div class="mt-6 sm:px-10 sm:w-[70%] w-[100%]">
        <div class="relative w-full h-[200px] mb-4 bg-gray-300 dark:bg-gray-700 overflow-hidden flex items-center justify-center">
            <img
                src="{{ Storage::url($community->image) }}"
                alt="{{ $community->name }}'s profile image"
                class="object-contain h-full w-full"
            />
            <h1 class="text-lg font-bold mb-4">{{ $community->name }} </h1>
        </div>
        {{-- Global Success/Error Flash Messages --}}
        @if (session()->has('message'))
            <div class="bg-green-100 fixed right-4 top-4 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                {{ session('message') }}
            </div>
        @endif

        <div x-data="{ isOpen: @entangle('isOpen').live }" class="w-full border border-gray-300 rounded-lg px-4 py-2 mb-10 bg-white dark:bg-gray-800 dark:border-gray-700">
            {{-- Main form to create a post --}}
            <form wire:submit.prevent="savePost"> {{-- Calls savePost method --}}
                <div class="flex justify-between">
                    <div>
                        {{-- Check if a profile photo URL exists --}}
                        @if (!empty(Auth::user()->profile)) {{-- Changed from $user->profile to $user->profile --}}
                            <img
                                class="border rounded-full w-[45px] h-[45px] object-cover mr-4"
                                src="{{ Storage::url(Auth::user()->profile) }} "
                            />
                        @else
                            {{-- Fallback: Display initials if no profile photo --}}
                            <div
                                class="flex items-center justify-center border rounded-full w-[45px] h-[45px] mr-4 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-bold text-lg uppercase"
                            >
                                @php
                                    $nameParts = explode(' ', Auth::user()->name);
                                    $initials = '';
                                    if (count($nameParts) > 0) {
                                        $initials .= strtoupper(substr($nameParts[0], 0, 1));
                                    }
                                    if (count($nameParts) > 1) {
                                        $initials .= strtoupper(substr(end($nameParts), 0, 1));
                                    } elseif (count($nameParts) == 1 && strlen($nameParts[0]) > 1) {
                                        $initials = strtoupper(substr($nameParts[0], 0, 2));
                                    }
                                    if (empty($initials) && !empty(Auth::user()->name)) {
                                        $initials = strtoupper(substr(Auth::user()->name, 0, 1));
                                    } elseif (empty($initials)) {
                                        $initials = '?';
                                    }
                                @endphp
                                {{ $initials }}
                            </div>
                        @endif
                    </div>
                    <textarea
                        wire:model="message"
                        class="border border-gray-300 w-[90%] dark:border-gray-600 rounded-lg h-[45px] resize-none bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:border-orange-500 dark:focus:border-orange-600 focus:ring-0 p-3"
                        placeholder="What's new in the community? ?"></textarea>
                </div>
                {{-- Specific error for the message field --}}
                @error('message') <span class="error text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                <div class="flex justify-end mt-2">
                    <flux:button type="button" variant="second" class="mr-6" @click="isOpen = true">
                        {{ __('Add image') }}
                        <i class="mdi mdi-camera-outline ml-2"></i>
                    </flux:button>

                    <flux:button type="submit" variant="primary">
                        {{ __('Send') }}
                        <i class="mdi mdi-send-outline ml-2"></i>
                    </flux:button>
                </div>
            </form>

            {{-- Modal for adding images --}}
            <div x-show="isOpen" x-cloak class="div-fond fixed z-60 inset-0 flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white p-6 rounded-lg shadow-lg sm:w-1/2 w-7/8 dark:bg-gray-800 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4 text-center">Créer une publication</h3>

                    {{-- Image preview section --}}
                    <div x-if="$wire.imagePreviewUrls.length > 0" class="mb-3 w-full max-h-[250px] overflow-auto flex flex-row space-x-2">
                        <template x-for="url in $wire.imagePreviewUrls" :key="url">
                            <img :src="url" class="object-cover h-full max-w-[150px] rounded">
                        </template>
                    </div>
                    <div x-if="!$wire.imagePreviewUrls.length" class="mb-3">
                        <p class="text-gray-500 dark:text-gray-400">Aucune image sélectionnée pour l'instant.</p>
                    </div>

                    {{-- File input field --}}
                    <input type="file" wire:model.live="images" multiple class="w-full border rounded p-2 mb-3 dark:bg-gray-700 dark:border-gray-600 text-gray-900 dark:text-gray-100">
                    {{-- Specific error for images --}}
                    @error('images.*') <span class="error text-red-500 text-xs">{{ $message }}</span> @enderror

                    {{-- Textarea for message in the modal --}}
                    <textarea wire:model="message" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg h-24 resize-none bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:border-orange-500 dark:focus:border-orange-600 focus:ring-0 p-3 mb-3" placeholder="Votre message"></textarea>
                    {{-- Specific error for message in the modal --}}
                    @error('message') <span class="error text-red-500 text-xs">{{ $message }}</span> @enderror

                    {{-- Modal action buttons --}}
                    <div class="flex justify-end mt-4">
                        <button @click="isOpen = false" type="button" class="mr-2 bg-gray-300 dark:bg-gray-600 dark:text-gray-100 px-4 py-2 rounded">Annuler</button>
                        <button wire:click="savePost" type="button" class="bg-blue-500 text-white px-4 py-2 rounded" :disabled="$errors->has('message') || $errors->has('images.*') || !$wire.message">Publier</button> {{-- Calls savePost method --}}
                    </div>
                </div>
            </div>
        </div>
        @if (session()->has('message'))
            <div class="bg-green-100 fixed right-4 top-4 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                {{ session('message') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Oops!</strong>
                <span class="block sm:inline">There are some problems with your submission.</span>
                <ul class="mt-3 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- Existing post display sections (unchanged, just included for completeness) --}}
        @forelse($posts as $post)
            <div x-data="{ openModal_{{ $post->id }}: false }" class="w-full bg-white rounded-lg mb-10">
                <div class="flex justify-between items-center py-2 px-4">
                    <div class="flex items-center">
                        <a href="{{ route('users.account.view', $post->user->id) }}">
                            <div>
                                @if (!empty($post->user->profile))
                                    <img
                                        class="border rounded-full w-[45px] h-[45px] object-cover mr-4"
                                        src="{{ Storage::url($post->user->profile) }} "
                                    />
                                @else
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

                    {{-- Report Button integrated here --}}
                    <div class="ml-4 mr-4">
                        <livewire:report-button :reportable-id="$post->id" :reportable-type="get_class($post)" />
                    </div>

                    {{-- CORRECTED: Reverted @click for comment modal --}}
                    <button @click="openModal_{{ $post->id }} = true"
                            class="flex items-center text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition focus:outline-none">
                        <i class="mdi mdi-comment-outline text-2xl ml-1"></i>
                        <span class="text-sm ml-1 text-orange-600 dark:text-orange-400">{{ $post->comments_count }}</span>
                    </button>
                </div>
                <div x-show="openModal_{{ $post->id }}" x-cloak class="div-fond fixed w-full z-60 inset-0 flex items-center justify-center bg-black bg-opacity-50">
                    <div class="flex flex-col px-6 pb-4 w-2/3 bg-white rounded-lg max-h-8/10 box-border relative">
                        <div class="border-b-1 border-gray-300">
                            <h1 class="py-4 text-center text-2xl font-bold">Comment publication of {{ $post->user->name }}</h1>
                            <button @click="openModal_{{ $post->id }} = false" class="absolute top-0 right-6 text-2xl font-bold">
                                &times;
                            </button>
                        </div>
                        <div class="overflow-y-auto no-scrollbar">
                            @if($post->postsImages->isNotEmpty())
                                <div class="relative w-full px-2 {{ $post->postsImages->count() > 1 ? 'grid grid-cols-2 gap-1' : '' }} overflow-hidden">
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
                                    @error('newCommentContent.' . $post->id) <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    <div class="flex justify-end mt-2">
                                        <button type="submit" class="flex items-center text-xs p-2 bg-orange-600 rounded-lg text-white hover:bg-orange-700 transition">
                                            Commenter <i class="mdi mdi-send-outline ml-2"></i>
                                        </button>
                                    </div>
                                </form>

                                @forelse($post->comments as $comment)
                                    <div class="bg-white dark:bg-gray-800 p-3 rounded-lg mb-2 shadow-sm border border-gray-200 dark:border-gray-700">
                                        <div class="flex items-start">
                                            <a href="{{ route('users.account.view', $comment->user->id) }}">
                                                <div>
                                                    @if (!empty($comment->user->profile))
                                                        <img
                                                            class="border rounded-full w-[35px] h-[35px] object-cover mr-4"
                                                            src="{{ Storage::url($comment->user->profile) }} "
                                                        />
                                                    @else
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
                                                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $comment->comment }}</p>

                                                <form wire:submit.prevent="addComment({{ $post->id }}, {{ $comment->id }})" class="mt-2">
                                                    <textarea
                                                        wire:model="newCommentContent.{{ $comment->id }}"
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

                                                @if($comment->replies->isNotEmpty())
                                                    <div class="mt-3 ml-6 border-l-2 border-gray-300 dark:border-gray-600 pl-3">
                                                        @foreach($comment->replies as $reply)
                                                            <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded-lg mb-1 shadow-sm border border-gray-100 dark:border-gray-600">
                                                                <div class="flex items-start">
                                                                    <a href="{{ route('users.account.view', $reply->user->id) }}">
                                                                        <div>
                                                                            @if (!empty($reply->user->profile))
                                                                                <img
                                                                                    class="border rounded-full w-[35px] h-[35px] object-cover mr-4"
                                                                                    src="{{ Storage::url($reply->user->profile) }} "
                                                                                />
                                                                            @else
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
                                                                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $reply->comment }}</p>
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
            <p class="text-center text-gray-500 dark:text-gray-400 text-lg">No posts yet for this community.</p>
        @endforelse

    </div>
    <div class="fixed right-2 top-0 w-[25%] py-2 box-border sm:block hidden">
        <livewire:user.section-home />
    </div>
</div>