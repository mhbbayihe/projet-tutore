<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{

    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        $socialUser = Socialite::driver($provider)->stateless()->user();

        $user = User::firstOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                'email' => $socialUser->getEmail(),
                'provider_id' => $socialUser->getId(),
                'provider' => $provider,
                'password' => bcrypt(uniqid()),
            ]
        );

        Auth::login($user, true);

        return match ($user->role) {
            'super-admin', 'admins' => redirect()->route('dashboard'),
            'users' => redirect()->route('home'),
            default => abort(403, 'Role not recognized'),
        };
    }

}
