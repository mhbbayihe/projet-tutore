<x-layouts.user-app :title="__('Dashboard')">
@php
$showFull = [];
@endphp
    <div class="box-border sm:px-0 px-1">
        @if (session('success'))
            <div class="bg-green-400 border border-green-400 text-white px-4 py-3 rounded  fixed z-100 right-2 mb-4" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-400 border border-red-400 text-white px-4 py-3 rounded  fixed z-100 right-2  mb-4" role="alert">
                {{ session('error') }}
            </div>
        @endif
        <div class="relative w-full h-[400px] overflow-hidden">
            @if ($user->profile)
                <img
                    src="{{ Storage::url($user->profile) }}"
                    alt="{{ $user->name }}'s profile image" 
                    class="object-cover w-full h-full" 
                />
            @else
                <div class="w-full h-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400 text-xl font-bold">
                    No Image
                </div>
            @endif
        </div>
        <div class="border-b-2 border-gray-300 h-20 w-full flex items-center justify-between sm:px-20 px-1 box-border">
            <div>
                <p class="text-sm flex text-orange-600">12 followers</p>
            </div>
            <div>
                <form action="{{ route('users.invitation.send', $user->id) }}" method="post">
                    @csrf
                    @method('POST')
                    <button type="submit" class="text-sm bg-blue-600 text-white p-2 px-2 rounded-lg ">Send invitation <i class="mdi mdi-send-outline ml-2"></i></button>
                </form>
            </div>
        </div>
        <div class="flex sm:flex-row flex-col h-screen">
            <div class="sm:w-[40%] w-full pr-5 box-border relative">
                <div class="sticky top-0 p-4">
                    <h1 class=" text-xl font-bold mb-4">Informations</h1>
                    <h2 class=" text-lg font-bold mb-2">Generals Informations</h2>
                    <div>
                        <div class="flex mb-2">
                            <h4><i class="mr-4 text-orange-600 mdi mdi-email-outline"></i></h4>{{ $user->email }}
                        </div>
                        @if ($user->detailsusers)
                            <div class="flex mb-2">
                                <h4> <i class="mr-4 text-orange-600 mdi mdi-map"></i> </h4>{{ $user->detailsusers->residence}}, {{ $user->detailsusers->city}}, {{ $user->detailsusers->country}} 
                            </div>
                            <div class="flex mb-2">
                                <h4> <i class="mr-4  text-black mdi mdi-github"></i> </h4>{{ $user->detailsusers->github_link}}
                            </div>
                            <div class="flex mb-2">
                                <h4> <i class="mr-4 text-blue-600 mdi mdi-linkedin"></i>  </h4>{{ $user->detailsusers->linkedin_link}}
                            </div>
                            <div class="flex mb-2">
                                <h4> <i class="mr-4 text-blue-600 mdi mdi-web"></i></h4><a class="text-blue-600" href="">{{ $user->detailsusers->website_link}}</a>
                            </div>
                            <div class="flex mb-2">
                                <h4> <i class="mr-4 text-orange-600 mdi mdi-gender-female"></i></h4>24 years
                            </div>
                        @endif
                    </div>
                    <h2 class="mt-4 text-lg font-bold mb-2">School Informations</h2>
                    <div>
                        @forelse ($user->schoolsusers as $school)
                            <div class="flex items-center mb-2">
                                <h4> <i class="mr-4 text-orange-600 mdi mdi-school-outline"></i> </h4>
                                <p class="ml-2">{{ $school->name }} ({{ $school->start_date->format('Y') }} - {{ $school->end_date->format('Y') }})</p>
                            </div>
                        @empty
                            <p>No school information added yet.</p>
                        @endforelse
                    </div>
                    @if ($user->detailsusers)
                        <h2 class="mt-4 text-lg font-bold mb-2">Bibiographie</h2>
                        <div>
                            <div class="mb-2">
                                <h4><Icon class="mr-4 text-orange-600" path={mdiSchoolOutline} size={0.8} /> </h4>
                                <p class="text-sm">{{ $user->detailsusers->biography }}
                                </p>
                            </div>
                        </div>
                    @endif 
                </div>
            </div>
            <div class="sm:w-[60%] w-full">
                <h1 class=" text-xl font-bold mb-4">Post</h1>
                <div class=" h-screen overflow-y-auto no-scrollbar">
                    @forelse($user->post as $post)
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
                                <div class="flex flex-col px-6 pb-4 sm:w-2/3 w-7/8 bg-white rounded-lg max-h-8/10 box-border relative">
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
            </div>
        </div>
    </div>
</x-layouts.user-app>
