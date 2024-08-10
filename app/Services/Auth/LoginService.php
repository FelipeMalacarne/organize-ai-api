<?php

namespace App\Services\Auth;

interface LoginService
{
    public function login(
        string $email,
        string $password,
        ?string $deviceName = 'default'
    ): string;

    public function logout(): void;

    public function logoutAll(): void;

    public function logoutDevice(string $deviceId): void;
}
