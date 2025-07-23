<x-layouts.app :title="__('Dashboard')">
    <div class="box-border sm:px-20 px-1">
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
                
                    @if ($user->status === 1)
                        <button type="submit" class="text-sm bg-blue-600 text-white p-2 px-2 rounded-lg "> Active</button>
                    @else
                        <button type="submit" class="text-sm bg-red-600 text-white p-2 px-2 rounded-lg ">Blocked</button>
                    @endif
                
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
                    @forelse($user->post as $posts)
                        <div x-data="{ openModal_{{ $posts->id }}: false }" class="w-full bg-white rounded-lg mb-10">
                            <div class="flex justify-between items-center py-2 px-4">
                                <div class="flex items-center">
                                    <div>
                                        <img
                                            class="border rounded-full mr-4 w-[45px]"
                                            src="/images/profile/team2.jpg"
                                            alt="Vercel logomark"
                                        />
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold">{{ $user->name }}</p>
                                        <p class="text-xs font-thin text-gray-400">{{ $user->surname }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <p class="text-sm mr-4">{{ $posts->created_at->diffForHumans() }} </p><i class="mdi mdi-dots-horizontal text-2xl"></i>
                                </div>
                            </div>
                            @if($posts->postsImages->isNotEmpty())
                                {{-- Condition pour afficher en grille si plus d'une image, sinon en une seule colonne --}}
                                <div class="relative w-full px-2 {{ $posts->postsImages->count() > 1 ? 'grid grid-cols-2 gap-1' : '' }} overflow-hidden">
                                    @foreach($posts->postsImages as $postImage)
                                        <img src="{{ Storage::url($postImage->image) }}"
                                            alt="Image du post : {{ $posts->text ? Str::limit($posts->text, 50) : 'Pas de description' }}"
                                            class="object-cover w-full h-[400px]"
                                        />
                                    @endforeach
                                </div>
                            @endif
                            <div class="px-4 py-4 text-justify">
                                <p class="text-sm">{{ $posts->text }}
                                </p>
                            </div>
                            <div class="py-2 px-6 border-t-1 border-gray-300 pb-4 flex items-center justify-between">
                                <button wire:click="togglePostLike({{ $posts->id }})" class="flex items-center text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-500 transition focus:outline-none mr-4">
                                    @if(Auth::check() && $posts->isLikedByUser(Auth::user()))
                                        <i class="mdi mdi-heart text-orange-600 text-2xl"></i>
                                    @else
                                        <i class="mdi mdi-heart-outline text-2xl"></i>
                                    @endif
                                    <span class="text-sm ml-1 text-orange-600 dark:text-orange-400">{{ $posts->likes_count }}</span>
                                </button>
                                <button @click="openModal_{{ $posts->id }} = true"
                                        class="flex items-center text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition focus:outline-none">
                                    <i class="mdi mdi-comment-outline text-2xl ml-1"></i>
                                    <span class="text-sm ml-1 text-orange-600 dark:text-orange-400">{{ $posts->comments_count }}</span>
                                </button>
                            </div>
                            {{-- Commentaires --}}
                            <div x-show="openModal_{{ $posts->id }}" class="div-fond fixed w-full z-60 inset-0 flex items-center  justify-center bg-black bg-opacity-50">
                                <div class="flex flex-col px-6 pb-4 w-2/3 bg-white rounded-lg max-h-8/10 box-border relative">
                                    <div class="border-b-1 border-gray-300">
                                        <h1 class="py-4 text-center text-2xl font-bold">Comment publication of {{ $user->name }}</h1>
                                        <button @click="openModal_{{ $posts->id }} = false" class="absolute top-0 right-6 text-2xl font-bold">
                                            &times;
                                        </button>
                                    </div>
                                    <div class="overflow-y-auto no-scrollbar">
                                        @if($posts->postsImages->isNotEmpty())
                                            {{-- Condition pour afficher en grille si plus d'une image, sinon en une seule colonne --}}
                                            <div class="relative w-full {{ $posts->postsImages->count() > 1 ? 'grid grid-cols-2 gap-1' : '' }} overflow-hidden">
                                                @foreach($posts->postsImages as $postImage)
                                                    <img src="{{ Storage::url($postImage->image) }}"
                                                        alt="Image du post : {{ $posts->content ? Str::limit($posts->content, 50) : 'Pas de description' }}"
                                                        class="object-cover w-full h-[250px]"
                                                    />
                                                @endforeach
                                            </div>
                                        @endif
                                        <div class="px-1 text-justify py-4">
                                            <p class="text-sm">{{ $posts->text }}
                                            </p>
                                        </div>
                                        <div class="bg-gray-100 rounded-lg p-4 dark:bg-gray-700 mt-6">
                                            <div class="flex justify-between items-center mb-3">
                                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Commentaires ({{ $posts->comments_count }})</p>
                                            </div>
                                            <form wire:submit.prevent="addComment({{ $posts->id }})" class="mb-4">
                                                <textarea
                                                    wire:model="newCommentContent.{{ $posts->id }}"
                                                    class="border border-gray-300 rounded-lg w-full h-[60px] resize-none bg-white dark:bg-gray-600 dark:border-gray-500 dark:text-gray-100 focus:outline-none focus:border-orange-500 focus:ring-0 p-2 text-sm"
                                                    placeholder="Écrire un commentaire..."
                                                ></textarea>
                                                @error('newCommentContent.' . $posts->id) <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                                <div class="flex justify-end mt-2">
                                                    <button type="submit" class="flex items-center text-xs p-2 bg-orange-600 rounded-lg text-white hover:bg-orange-700 transition">
                                                        Commenter <i class="mdi mdi-send-outline ml-2"></i>
                                                    </button>
                                                </div>
                                            </form>

                                            {{-- Liste des commentaires --}}
                                            @forelse($posts->comments as $comment)
                                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg mb-2 shadow-sm border border-gray-200 dark:border-gray-700">
                                                    <div class="flex items-start">
                                                        <img class="border rounded-full w-[30px] h-[30px] object-cover mr-3"
                                                                src="{{ $comment->user->profile ?? '/images/profile/default-avatar.jpg' }}"
                                                                
                                                        />
                                                        <div class="flex-1">
                                                            <div class="flex items-center justify-between">
                                                                <p class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $comment->user->username ?? $comment->user->name }}</p>
                                                                <p class="text-xs font-thin text-gray-500 dark:text-gray-400">{{ $comment->created_at->diffForHumans() }}</p>
                                                            </div>
                                                            <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $comment->comment }}</p> {{-- <-- Utilisez $comment->comment --}}

                                                            {{-- Formulaire pour répondre à un commentaire --}}
                                                            <form wire:submit.prevent="addComment({{ $posts->id }}, {{ $comment->id }})" class="mt-2">
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
                                                                                <img class="border rounded-full w-[25px] h-[25px] object-cover mr-2"
                                                                                        src="{{ $reply->user->profile?? '/images/profile/default-avatar.jpg' }}"
                                                                                />
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
                        <p class="text-center">No Post</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>