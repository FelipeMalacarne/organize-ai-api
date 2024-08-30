<?php

namespace App\Contracts;

use App\Models\User;

interface AuthService
{
    public function findOrCreateUser($socialUser, $provider): User;

    public function linkSocialAccount(User $user, $socialUser, $provider): void;
}
