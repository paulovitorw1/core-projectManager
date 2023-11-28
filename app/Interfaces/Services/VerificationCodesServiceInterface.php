<?php

namespace App\Interfaces\Services;

use App\Models\User;

interface VerificationCodesServiceInterface
{
    public function create(array $user);
    public function validateOTP(array $data);
    public function getUser(string $email): User | null;
}