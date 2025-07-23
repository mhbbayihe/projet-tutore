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
        <div class="relative w-full h-[400px]  overflow-hidden">
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
        <div x-data="{ modal: false, imagePreview: '{{ $user->profile ? Storage::url($user->profile) : '' }}' }" class="border-b-2 border-gray-300 h-20 w-full flex items-center justify-end sm:px-20 px-1 box-border">
            <div>
                <button @click="modal = true" class="ml-6 bg-gray-200 rounded-lg text-sm p-2 border border-gray-300 hover:bg-orange-600 hover:text-white transition duration-200">
                    Edit profile picture
                </button>
            </div>
            <div x-show="modal" x-cloak class="div-fond fixed z-10 inset-0 flex items-center justify-center">
                <div @click.away="modal = false; imagePreview = '{{ $user->profile ? Storage::url($user->profile) : '' }}';" class="bg-white p-4 relative rounded-lg sm:w-1/2 w-7/8">
                    <div class="mb-4">
                        <h1 class="text-xl font-bold text-center">Update Profile Picture</h1>
                    </div>
                    <div>
                        <form action="{{ route('users.edit.profile') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT') 
                            <div class="w-full h-[250px] relative flex items-center justify-center bg-gray-100 rounded-lg mb-4 overflow-hidden">
                                {{-- Display current profile picture or a placeholder --}}
                                <template x-if="imagePreview">
                                    <img :src="imagePreview" alt="Profile Picture Preview" class="max-h-full max-w-full object-contain rounded-lg">
                                </template>
                                <template x-if="!imagePreview">
                                    <img src="{{ Storage::url($user->profile) }}" alt="Profile Picture Preview" class="max-h-full max-w-full object-contain rounded-lg">
                                </template>
                            </div>
                            <input type="file" name="profile_picture" id="profile_picture"
                                class="block w-full text-zinc-700 dark:text-zinc-300 mb-4"
                                @change="
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        imagePreview = e.target.result;
                                    };
                                    reader.readAsDataURL($event.target.files[0]);
                                "
                            />
                            @error('profile_picture')
                                <p class="text-red-500 text-xs mt-1 mb-2">{{ $message }}</p>
                            @enderror
                            <div class="flex justify-end">
                                <button type="submit" class="bg-orange-600 p-2 text-white rounded-lg px-4 hover:bg-orange-700 transition duration-200">Update Picture</button>
                            </div>
                        </form>
                    </div>
                    <button class="absolute right-3 top-3 text-gray-500 hover:text-gray-700"
                        @click="modal = false; imagePreview = '{{ $user->profile ? Storage::url($user->profile) : '' }}';">
                        <i class="mdi mdi-close text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="flex sm:flex-row flex-col h-screen">
            <div class="sm:w-[40%] w-full pr-5 box-border relative">
                <div class=" p-4">
                    <h1 class=" text-xl font-bold mb-4">Informations</h1>
                    <div x-data="{ general: false }"  class="flex items-center justify-between">
                        <h2 class="mt-4 text-lg font-bold mb-2">Generals informations</h2>
                        <button @click="general = true" class="ml-6 bg-gray-200 rounded-lg px-2 border-1 border-gray-300 hover:transform hover:bg-orange-600 hover:text-white "><i class="mdi mdi-pencil-box-outline text-2xl"></i></button>
                        <div x-show="general">
                            <div class="div-fond fixed z-10 inset-0 flex items-center justify-center">
                                <div class="bg-white p-4 relative rounded-lg sm:w-2/3 w-7/8">
                                    <div class="mb-4">
                                        <h1 class="text-xl font-bold text-center">Edit profile</h1>
                                    </div>
                                    <div>
                                        <form action="{{ route('users.edit.general') }}" class='p-4' method="post">
                                            @csrf
                                            @method('PUT') 
                                            <div class='pb-4 flex justify-between'>
                                                <div class="w-[49%]">
                                                    <label class='font-bold' htmlFor="name">Name:</label>
                                                    <input class='block w-full border-1 border-gray-200 rounded-sm h-[35px] p-2' type="text" placeholder="Enter your name" value="{{ $user->name }}" name="name" />
                                                </div>
                                                <div class="w-[49%]">
                                                    <label class='font-bold' htmlFor="name">Surame:</label>
                                                    <input class='block w-full border-1 border-gray-200 rounded-sm h-[35px] p-2' type="text" placeholder="Enter your surname" value="{{ $user->surname }}" name="surname" />
                                                </div>
                                            </div>
                                            <div class='pb-4 flex justify-between'>
                                                <div class="w-[49%]">
                                                    <label class='font-bold' htmlFor="name">Birthday:</label>
                                                    <input class='block w-full border-1 border-gray-200 rounded-sm h-[35px] p-2' type="date" placeholder="Enter your birthday" value="{{ optional($user->detailsusers)->birthday }}" name="birthday" />
                                                </div>
                                                <div class="w-[49%]">
                                                    <label class='font-bold' htmlFor="name">Country:</label>
                                                    <input class='block w-full border-1 border-gray-200 rounded-sm h-[35px] p-2' type="text" placeholder="Enter your country" value="{{ optional($user->detailsusers)->country }}" name="country" />
                                                </div>
                                            </div>
                                            <div class='pb-4 flex justify-between'>
                                                <div class="w-[49%]">
                                                    <label class='font-bold' htmlFor="name">Gender:</label>
                                                    <select class='block w-full border-1 border-gray-200 rounded-sm h-[35px] p-2' name="gender" id="">
                                                        <option value="male">Male</option>
                                                        <option value="female">Female</option>
                                                    </select>
                                                </div>
                                                <div class="w-[49%]">
                                                    <label class='font-bold' htmlFor="name">Phone:</label>
                                                    <input class='block w-full border-1 border-gray-200 rounded-sm h-[35px] p-2' type="text" placeholder="Enter your phone number" value="{{ $user->phone }}" name="phone" />
                                                </div>
                                            </div>
                                            <div class='pb-4 flex justify-between'>
                                                <div class="w-[49%]">
                                                    <label class='font-bold' htmlFor="name">City:</label>
                                                    <input class='block w-full border-1 border-gray-200 rounded-sm h-[35px] p-2' type="text" placeholder="Enter your city" value="{{ optional($user->detailsusers)->city }}" name="city"/>
                                                </div>
                                                <div class="w-[49%]">
                                                    <label class='font-bold' htmlFor="name">Residence:</label>
                                                    <input class='block w-full border-1 border-gray-200 rounded-sm h-[35px] p-2' type="text" placeholder="Enter your residence" value="{{ optional($user->detailsusers)->residence }}" name="residence" />
                                                </div>
                                            </div>
                                            <h1 class="pb-4 font-bold text-lg text-orange-400">Social network link</h1>
                                            <div class='pb-4'>
                                                <input class='block w-full border-1 border-gray-200 rounded-sm h-[35px] p-2' type="text"  placeholder="Enter link of your web site" value="{{ optional($user->detailsusers)->website_link }}" name="website_link" />
                                            </div>
                                            <div class='pb-4 flex justify-between'>
                                                <div class="w-[49%]">
                                                    <input class='block w-full border-1 border-gray-200 rounded-sm h-[35px] p-2' type="text" placeholder="Enter link of your profile github" value="{{ optional($user->detailsusers)->github_link }}" name="github_link" />
                                                </div>
                                                <div class="w-[49%]">
                                                    <input class='block w-full border-1 border-gray-200 rounded-sm h-[35px] p-2' type="text" placeholder="Enter link of your profile linkledn"  value="{{ optional($user->detailsusers)->linkedin_link }}" name="linkedin_link"  />
                                                </div>
                                            </div>
                                            <div class="flex justify-between">
                                                <button @click="general = false" type="reset" class="bg-gray-500 p-2 text-white rounded-lg mt-4 px-4">Close</button>
                                                <button class="bg-orange-600 p-2 text-white rounded-lg mt-4 px-4">Edit</button>
                                            </div>
                                        </form>
                                    </div>
                                    <button class="absolute right-3 top-3" @click="general = false"><i class="mdi mdi-close"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="flex mb-2">
                            <h4><i class="mdi mdi-email-outline"></i></h4>{{ $user->email }}
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
                </div>
            </div>
            <div class="sm:w-[60%] w-full">
                <div class=" p-4">
                    <h1 class=" text-xl font-bold mb-4">Informations</h1>
                    <div x-data="{ profile: false }"  class="flex items-center justify-between">
                        <h2 class="mt-4 text-lg font-bold mb-2">Generals informations</h2>
                        <button @click="profile = true" class="ml-6 bg-gray-200 rounded-lg px-2 border-1 border-gray-300 hover:transform hover:bg-orange-600 hover:text-white "><i class="mdi mdi-plus-circle text-2xl"></i></button>
                        <div x-show="profile">
                            <div class="div-fond fixed z-10 inset-0 flex items-center justify-center">
                                <div class="bg-white p-4 relative rounded-lg sm:w-1/3 w-7/8">
                                    <div class="mb-4">
                                        <h1 class="text-xl font-bold text-center">Add school</h1>
                                    </div>
                                    <div>
                                        <form action="{{ route('users.add.school') }}" class='p-4' method="post">
                                            @csrf
                                            @method('POST')
                                            <label class='font-bold' htmlFor="name">Name of the school:</label>
                                            <div class='pb-4'>
                                                <input class='block w-full border-1 border-gray-200 rounded-sm h-[35px] p-2' type="text"  placeholder="Enter the name of the school where you started." name="name" />
                                            </div> 
                                            <div class='pb-4 flex justify-between'>
                                                <div class="w-[49%]">
                                                    <label class='font-bold' htmlFor="name">Year start:</label>
                                                    <input class='block w-full border-1 border-gray-200 rounded-sm h-[35px] p-2' type="date" name="start" />
                                                </div>
                                                <div class="w-[49%]">
                                                    <label class='font-bold' htmlFor="name">Year is ending:</label>
                                                    <input class='block w-full border-1 border-gray-200 rounded-sm h-[35px] p-2' type="date" name="end" />
                                                </div>
                                            </div>
                                            <div class="flex justify-between">
                                                <button @click="profile = false" type="reset" class="bg-gray-500 p-2 text-white rounded-lg mt-4 px-4">Close</button>
                                                <button class="bg-orange-600 p-2 text-white rounded-lg mt-4 px-4">Add</button>
                                            </div>
                                        </form>
                                    </div>
                                    <button class="absolute right-3 top-3" @click="profile = false"><i class="mdi mdi-close"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        @forelse ($user->schoolsusers as $school)
                            <div class="flex items-center mb-2">
                                <h4> <i class="mdi mdi-school-outline"></i> </h4>
                                <p class="ml-2">{{ $school->name }} ({{ $school->start_date->format('Y') }} - {{ $school->end_date->format('Y') }})</p>
                                <div x-data={sool:false}>
                                    <button @click="sool=true"><i class="mdi mdi-delete-outline text-xl text-red-600"></i>
                                    </button>
                                    <div x-show="sool">
                                        <div class="div-fond fixed z-10 inset-0 flex items-center justify-center">
                                            <div class="bg-white p-4 relative rounded-lg sm:w-1/2 w-7/8">
                                                <div class="mb-4">
                                                    <h1 class="text-xl font-bold text-center">Delete school {{ $school->name }}</h1>
                                                </div>
                                                <div class="w-full">
                                                    <form class="w-full" action="{{ route('users.delete.school', $school->id) }}" method="post">
                                                        @csrf
                                                        @method('DELETE')
                                                        <div class="w-full flex justify-center text-red-600">
                                                            <i class="mdi mdi-alert text-[64px]" ></i>
                                                        </div>
                                                        <div class="w-full flex justify-between">
                                                            <button @click="sool=false" type="reset" class="p-3 rounded-lg bg-gray-400" >Back</button>
                                                            <button type="submit" class="p-3 rounded-lg bg-red-600 text-white" >Delete</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p>No school information added yet.</p>
                        @endforelse
                    </div>
                    <div x-data={bio:false} class="flex items-center justify-between">
                        <h2 class="mt-4 text-lg font-bold mb-2">Bibiographie</h2>
                        <button @click="bio=true" class="ml-6 bg-gray-200 rounded-lg px-2  border-1 border-gray-300 hover:transform hover:bg-orange-600 hover:text-white "><i class="mdi mdi-pencil-box-outline text-2xl"></i></button>
                        <div x-show="bio">
                            <div class="div-fond fixed z-10 inset-0 flex items-center justify-center">
                                <div class="bg-white p-4 relative rounded-lg sm:w-1/3 w-7/8">
                                    <div class="mb-4">
                                        <h1 class="text-xl font-bold text-center">Edit biography</h1>
                                    </div>
                                    <div>
                                        <form action="{{ route('users.edit.bio') }}" class='p-4' method="post">
                                            @csrf
                                            @method('PUT')
                                            <label class='font-bold' htmlFor="name">Bigraphy:</label>
                                            <div class='pb-4'>
                                                <textarea name="biography" class="w-full h-[100px] overflow-auto resize-none" id="">{{ optional($user->detailsusers)->biography }}</textarea>
                                            </div> 
                                            <div class="flex justify-between">
                                                <button @click="bio = false" type="reset" class="bg-gray-500 p-2 text-white rounded-lg mt-4 px-4">Close</button>
                                                <button class="bg-orange-600 p-2 text-white rounded-lg mt-4 px-4">Edit</button>
                                            </div>
                                        </form>
                                    </div>
                                    <button class="absolute right-3 top-3" @click="bio = false"><i class="mdi mdi-close"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="mb-2">
                            <h4><i class="mdi mdi-school-outline"></i> </h4>
                            <p class="text-base">{{ optional($user->detailsusers)->biography }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.user-app>
