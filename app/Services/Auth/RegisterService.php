<?php

namespace App\Services\Auth;

interface RegisterService
{
    public function register(
        string $email,
        string $password,
        string $name,
        ?string $deviceName = 'default'
    ): string;
}
