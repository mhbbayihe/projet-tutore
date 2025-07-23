<?php

use App\Models\User;
use App\Models\Community;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Storage; // Ensure this is imported for image display

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public int $step = 1;
    public array $selectedCommunities = [];

    // Removed: protected ?User $registeredUser = null;
    // User data will now be carried directly in public properties until step 2.


    /**
     * Handle incoming registration request for Step 1 (User details).
     */
    public function registerStep1(): void
    {
        // Validate only the first step's data.
        // The data will remain in public properties and not yet saved to DB.
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'phone' => ['required', 'string', 'max:15', 'min:9', 'unique:' . User::class],
            'password' => ['required', 'string', Rules\Password::defaults()],
        ]);

        // If validation passes, move to Step 2 without creating the user.
        $this->step = 2;
    }

    /**
     * Provides data for the view, specifically the list of communities.
     */
    public function with(): array
    {
        return [
            'communities' => Community::orderBy('name')->get(),
        ];
    }

    /**
     * Handle community selection and finalize registration (Step 2).
     */
    public function registerStep2(): void
    {
        // Re-validate ALL necessary data for user creation
        // and community selection before proceeding.
        $validatedData = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'phone' => ['required', 'string', 'max:15', 'min:9', 'unique:' . User::class],
            'password' => ['required', 'string', Rules\Password::defaults()],
            'selectedCommunities' => ['required', 'array', 'min:1'],
            'selectedCommunities.*' => ['exists:communities,id'], // Ensure selected IDs exist
        ]);

        // Hash the password before user creation
        $hashedPassword = Hash::make($this->password);

        // CREATE THE USER HERE, ONLY AT THE SECOND STEP
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => $hashedPassword, // Use the hashed password
        ]);

        // Attach selected communities to the newly created user
        $user->communities()->attach($this->selectedCommunities);

        // Fire the Registered event
        event(new Registered($user));

        // Log the user in
        Auth::login($user);

        // Redirect to the dashboard
        $this->redirectIntended(route('home', absolute: false), navigate: true);
    }

    /**
     * Go back to Step 1.
     */
    public function backToStep1(): void
    {
        $this->step = 1;
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header2 :title="__('Create an account')" />

    <x-auth-session-status class="text-center" :status="session('status')" />

    {{-- STEP 1: User Details --}}
    @if ($step === 1)
        <form wire:submit="registerStep1" class="flex flex-col gap-6">
            <flux:input
                wire:model="name"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Full name')"
            />
            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

            <flux:input
                wire:model="email"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />
            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

            <flux:input
                wire:model="phone"
                type="tel"
                required
                autocomplete="tel"
                :placeholder="__('e.g., +237 6xx xxx xxx')"
            />
            @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

            <flux:input
                wire:model="password"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                viewable
            />
            @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full">
                    {{ __('Next Step') }}
                </flux:button>
            </div>
        </form>
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300 dark:border-zinc-600"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="bg-white px-2 text-gray-500 dark:bg-zinc-900 dark:text-zinc-400">Or continue with</span>
            </div>
        </div>

        <div class="flex justify-between">
            <a href="{{ route('auth.provider', ['provider' => 'google'])  }}" class="bg-orange-600 ml-4 border-none rounded-full w-[40px] h-[40px] p-1  transition flex justify-center items-center text-xl">
                <i class="text-white mdi mdi-google"></i>
            </a>

            <a href="{{ route('auth.provider', ['provider' => 'facebook']) }}" class="bg-blue-600 ml-4 border-none rounded-full w-[40px] h-[40px] p-1  transition flex justify-center items-center text-xl">
                <i class="text-white mdi mdi-facebook"></i>
            </a>

            <a href="{{ route('auth.provider', ['provider' => 'github']) }}" class=" bg-black ml-4 border-none rounded-full w-[40px] h-[40px] p-1  transition flex justify-center items-center text-xl">
                <i class="text-white mdi mdi-github"></i>
            </a>
        </div>
    @endif

    {{-- STEP 2: Community Selection --}}
    @if ($step === 2)
        <form wire:submit="registerStep2" class="flex flex-col gap-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Select your Communities') }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Choose at least one community to join.') }}</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse ($communities as $community)
                    <label class="flex items-center space-x-3 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg shadow-sm cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                        <input
                            type="checkbox"
                            wire:model="selectedCommunities"
                            value="{{ $community->id }}"
                            class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500"
                        >
                        <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $community->name }}</span>
                        @if ($community->image_path)
                            <img src="{{ Storage::url($community->image_path) }}" alt="{{ $community->name }}" class="w-8 h-8 rounded-full object-cover ml-auto">
                        @endif
                    </label>
                @empty
                    <p class="text-gray-500 dark:text-gray-400 col-span-full">{{ __('No communities available at the moment.') }}</p>
                @endforelse
            </div>
            @error('selectedCommunities') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

            <div class="flex items-center justify-between mt-4">
                <flux:button type="button" wire:click="backToStep1">
                    {{ __('Back') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('Complete Registration') }}
                </flux:button>
            </div>
        </form>
    @endif


    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Already have an account?') }}
        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
</div>