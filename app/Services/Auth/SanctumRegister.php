<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SanctumRegister implements RegisterService
{
    public function register(
        string $email,
        string $password,
        string $name,
        ?string $deviceName = 'default'
    ): string {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        return $user->createToken($deviceName)->plainTextToken;
    }
}
