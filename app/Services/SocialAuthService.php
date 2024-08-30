<?php

namespace App\Services;

use App\Contracts\AuthService;
use App\Models\SocialAccount;
use App\Models\User;

class SocialAuthService implements AuthService
{
    public function findOrCreateUser($socialUser, $provider): User
    {
        $socialAccount = SocialAccount::where('provider_name', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($socialAccount) {
            return $socialAccount->user;
        }

        $user = User::where('email', $socialUser->getEmail())->first();
        if (! $user) {
            $user = User::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
            ]);
        }

        $this->linkSocialAccount($user, $socialUser, $provider);

        return $user;
    }

    public function linkSocialAccount(User $user, $socialUser, $provider): void
    {
        $user->socialAccounts()->create([
            'provider_name' => $provider,
            'provider_id' => $socialUser->getId(),
            'token' => $socialUser->token,
            'refresh_token' => $socialUser->refreshToken,
            'expires_in' => $socialUser->expiresIn,
        ]);
    }
}
