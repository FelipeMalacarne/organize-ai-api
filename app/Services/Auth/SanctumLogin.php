<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class SanctumLogin implements LoginService
{
    public function login(string $email, string $password, ?string $deviceName = 'default'): string
    {
        $user = User::where('email', $email)->first();
        if (! $user || ! Hash::check($password, $user->password)) {
            throw new AuthenticationException;
        }

        return $user->createToken($deviceName)->plainTextToken;
    }

    public function logout(): void
    {
        /**
         * @var User $user
         * */
        $user = auth()->user();

        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
    }

    public function logoutAll(): void
    {
        /**
         * @var User $user
         * */
        $user = auth()->user();
        $user->tokens()->delete();
    }

    public function logoutDevice(string $deviceId): void
    {
        /**
         * @var User $user
         * */
        $user = auth()->user();
        $user->tokens()->where('id', $deviceId)->delete();
    }
}
