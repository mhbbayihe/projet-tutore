<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'), // message: "These credentials do not match our records."
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $user = Auth::user();

        if ($user->role === 'super-admin' || $user->role === 'admins') {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
        } else {
            $this->redirectIntended(default: route('home', absolute: false), navigate: true);
        }
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Hello')" :description="__('Sign in to your account')" />

    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="login" class="flex flex-col gap-6">
        <flux:input
            wire:model="email"
            type="email"
            required
            autofocus
            autocomplete="email"
            placeholder="email@example.com"
        />
        @error('email')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror

        <flux:input
            wire:model="password"
            type="password"
            required
            autocomplete="current-password"
            :placeholder="__('Password')"
            viewable
        />
        @error('password')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror

        <div class="relative mt-0 mb-6">
            @if (Route::has('password.request'))
                <flux:link variant="subtle" class="absolute end-0 top-0 text-sm" :href="route('password.request')" wire:navigate>
                    {{ __('Forgot your password?') }}
                </flux:link>
            @endif
        </div>

        <flux:button variant="primary" type="submit" class="w-full">{{ __('Log in') }}</flux:button>
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

    @if (Route::has('register'))
        <div class="text-center text-sm text-zinc-600 dark:text-zinc-400">
            {{ __("Don't have an account?") }}
            <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
        </div>
    @endif
</div>


