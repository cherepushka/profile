<?php

namespace App\Services;

class UserService
{
    private readonly int $user_password_length;
    private readonly string $hash_algo;

    public function __construct()
    {
        $this->user_password_length = 8;
        $this->hash_algo = 'sha256';
    }

    /**
     * Generates user random password
     */
    public function generatePassword(): string
    {
        $length = $this->user_password_length;
        $pool = '123456789abcdefghijklmnpqrstuvwxyz';

        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }

    public function encryptUserData(string $user_data): string
    {
        return hash($this->hash_algo, $user_data);
    }
}
