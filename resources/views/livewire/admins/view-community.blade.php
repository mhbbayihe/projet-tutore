<div class="relative">
    <div class="mt-6 sm:px-10 w-[100%]">
        <div class="relative mb-4 w-full h-[200px] bg-gray-300 dark:bg-gray-700 overflow-hidden flex items-center justify-center">
            <img
                src="{{ Storage::url($community->image) }}"
                alt="{{ $community->name }}'s profile image"
                class="object-contain h-full w-full"
            />
            <h1 class="text-lg font-bold mb-4">{{ $community->name }} Community</h1>
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

        @forelse($posts as $post)
            <div x-data="{ openModal_{{ $post->id }}: false }" class="w-full border-1 border-gray-400 rounded-lg mb-10">
                <div class="flex justify-between items-center py-2 px-4">
                    <div class="flex items-center">
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
                                alt="Post image: {{ $post->content ? Str::limit($post->content, 50) : 'No description' }}"
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
                <div class="py-2 px-6 border-t-1 border-gray-300 pb-4 flex items-center justify-between">
                    <button><i class="mdi mdi-swap-horizontal text-2xl"></i></button>
                    <button @click="openModal_{{ $post->id }} = true"
                            class="flex items-center text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition focus:outline-none">
                        <i class="mdi mdi-comment-outline text-2xl ml-1"></i>
                        <span class="text-sm ml-1 text-orange-600 dark:text-orange-400">{{ $post->comments_count }}</span>
                    </button>
                </div>
                <div x-show="openModal_{{ $post->id }}" class="div-fond fixed w-full z-60 inset-0 flex items-center justify-center bg-black bg-opacity-50">
                    <div class="flex flex-col px-6 pb-4 w-2/3 bg-white rounded-lg max-h-8/10 box-border relative">
                        <div class="border-b-1 border-gray-300">
                            <h1 class="py-4 text-center text-2xl font-bold">Comment publication of {{ $post->user->name }}</h1>
                            <button @click="openModal_{{ $post->id }} = false" class="absolute top-0 right-6 text-2xl font-bold">
                                &times;
                            </button>
                        </div>
                        <div class="overflow-y-auto no-scrollbar">
                            @if($post->postsImages->isNotEmpty())
                                <div class="relative w-full {{ $post->postsImages->count() > 1 ? 'grid grid-cols-2 gap-1' : '' }} overflow-hidden">
                                    @foreach($post->postsImages as $postImage)
                                        <img src="{{ Storage::url($postImage->image) }}"
                                            alt="Post image: {{ $post->content ? Str::limit($post->content, 50) : 'No description' }}"
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
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Comments ({{ $post->comments_count }})</p>
                                </div>
                                <form wire:submit.prevent="addComment({{ $post->id }})" class="mb-4">
                                    <textarea
                                        wire:model="newCommentContent.{{ $post->id }}"
                                        class="border border-gray-300 rounded-lg w-full h-[60px] resize-none bg-white dark:bg-gray-600 dark:border-gray-500 dark:text-gray-100 focus:outline-none focus:border-orange-500 focus:ring-0 p-2 text-sm"
                                        placeholder="Write a comment..."
                                    ></textarea>
                                    @error('newCommentContent.' . $post->id) <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    <div class="flex justify-end mt-2">
                                        <button type="submit" class="flex items-center text-xs p-2 bg-orange-600 rounded-lg text-white hover:bg-orange-700 transition">
                                            Comment <i class="mdi mdi-send-outline ml-2"></i>
                                        </button>
                                    </div>
                                </form>

                                @forelse($post->comments as $comment)
                                    <div class="bg-white dark:bg-gray-800 p-3 rounded-lg mb-2 shadow-sm border border-gray-200 dark:border-gray-700">
                                        <div class="flex items-start">
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
                                                        placeholder="Reply..."
                                                    ></textarea>
                                                    @error('newCommentContent.' . $comment->id) <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                                    <div class="flex justify-end mt-1">
                                                        <button type="submit" class="flex items-center text-xs p-1 bg-blue-500 rounded-lg text-white hover:bg-blue-600 transition">
                                                            Reply <i class="mdi mdi-reply ml-1"></i>
                                                        </button>
                                                    </div>
                                                </form>

                                                @if($comment->replies->isNotEmpty())
                                                    <div class="mt-3 ml-6 border-l-2 border-gray-300 dark:border-gray-600 pl-3">
                                                        @foreach($comment->replies as $reply)
                                                            <div class="bg-gray-50 dark:bg-gray-700 p-2 rounded-lg mb-1 shadow-sm border border-gray-100 dark:border-gray-600">
                                                                <div class="flex items-start">
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
                                    <p class="text-center text-gray-500 dark:text-gray-400 text-sm py-4">Be the first to comment!</p>
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
</div>