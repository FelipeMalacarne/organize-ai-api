<?php

namespace App\Http\Controllers;

use App\Contracts\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialMediaController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function redirectToProvider($provider): RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        $socialUser = Socialite::driver($provider)->stateless()->user();
        $user = $this->authService->findOrCreateUser($socialUser, $provider);

        Auth::login($user, true);

        // Generate Sanctum token after social login
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
    }

}
