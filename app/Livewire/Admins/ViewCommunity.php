<?php

namespace App\Livewire\Admins;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use App\Models\Community;
use App\Models\PostsImage;

class ViewCommunity extends Component
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
        $user = Auth::user();

        if (!Auth::check() || ($user->role !== 'super-admin' && $user->role !== 'admin')) {
            if (!Auth::check()) {
                session()->flash('error', 'You must be logged in to access the admin area.');
                $this->redirect(route('login'), navigate: true);
                return;
            } else {
                session()->flash('error', 'You do not have administrative access for this section.');
                $this->redirect(route('dashboard'), navigate: true);
                return;
            }
        }

        $this->community = $community;
        $this->loadPosts();
    }

    public function loadPosts()
    {
        $this->posts = $this->community->posts()->with([
            'user',
            'postsImages',
            'comments' => function ($query) {
                $query->with(['user', 'replies.user'])
                    ->whereNull('parent_id')
                    ->latest();
            },
        ])->withCount('comments')->latest()->get();

        foreach ($this->posts as $post) {
            $this->newCommentContent[$post->id] = '';
        }
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
        $this->resetErrorBag('images.*');
        $this->resetErrorBag('images');

        $this->imagePreviewUrls = [];
        if ($this->images) {
            foreach ($this->images as $index => $image) {
                try {
                    $this->validateOnly("images.{$index}");
                    $this->imagePreviewUrls[] = $image->temporaryUrl();
                } catch (\Illuminate\Validation\ValidationException $e) {
                }
            }
        }
    }

    public function savePost()
    {
        $this->validate([
            'message' => ['required_without:images', 'string', 'min:5', 'max:2000'],
            'images' => ['required_without:message', 'array', 'max:5'],
            'images.*' => ['nullable', 'image', 'max:2048', 'mimes:jpg,png,jpeg,gif,svg'],
        ]);

        if (!Auth::check()) {
            session()->flash('error', 'You must be logged in to publish.');
            return;
        }

        if (!$this->community || !$this->community->id) {
            session()->flash('error', 'The community could not be identified for publishing.');
            return;
        }

        try {
            $post = Post::create([
                'text' => $this->message,
                'user_id' => Auth::id(),
                'community_id' => $this->community->id,
            ]);

            if (!empty($this->images)) {
                foreach ($this->images as $image) {
                    $path = $image->store('posts_photos', 'public');
                    PostsImage::create([
                        'post_id' => $post->id,
                        'image' => $path,
                    ]);
                }
            }

            session()->flash('success', 'Post created successfully!');

            $this->reset(['isOpen', 'images', 'message', 'imagePreviewUrls']);
            $this->loadPosts();

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating post: ' . $e->getMessage());
            \Log::error('Post creation error: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
        }
    }

    public function createMainPost()
    {
        $this->validate([
            'newPostContent' => 'required|string|min:5|max:2000',
        ]);

        if (!Auth::check()) {
            session()->flash('error', 'You must be logged in to publish a post.');
            return;
        }

        try {
            Post::create([
                'user_id' => Auth::id(),
                'content' => $this->newPostContent,
                'community_id' => $this->community->id,
            ]);

            session()->flash('success', 'Main post created successfully!');
            $this->reset('newPostContent');
            $this->loadPosts();
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating main post: ' . $e->getMessage());
            \Log::error('Main post creation error: ' . $e->getMessage());
        }
    }

    public function addComment(int $postId, ?int $parentId = null)
    {
        if (!Auth::check()) {
            session()->flash('error', 'You must be logged in to comment.');
            return;
        }

        $validationKey = 'newCommentContent.' . ($parentId ?: $postId);

        $this->validate([
            $validationKey => 'required|string|min:1|max:500',
        ]);

        try {
            $commentData = [
                'user_id' => Auth::id(),
                'comment' => $this->newCommentContent[$parentId ?: $postId],
            ];

            if (is_null($parentId)) {
                $parent = Post::find($postId);
                if (!$parent) {
                    session()->flash('error', 'Post not found for comment.');
                    return;
                }
                $parent->comments()->create($commentData);
            } else {
                $parent = Comment::find($parentId);
                if (!$parent) {
                    session()->flash('error', 'Parent comment not found.');
                    return;
                }
                $commentData['post_id'] = $parent->post_id;
                $commentData['parent_id'] = $parent->id;
                $parent->replies()->create($commentData);
            }

            session()->flash('success', 'Comment added successfully!');
            unset($this->newCommentContent[$parentId ?: $postId]);
            $this->loadPosts();
        } catch (\Exception $e) {
            session()->flash('error', 'Error adding comment: ' . $e->getMessage());
            \Log::error('Comment add error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admins.view-community');
    }
}