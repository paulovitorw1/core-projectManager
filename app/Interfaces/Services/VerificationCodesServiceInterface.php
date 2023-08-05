<?php

namespace App\Interfaces\Services;

use App\Models\User;

interface VerificationCodesServiceInterface
{
    public function create(User $user);
    public function validateOTP(array $data);
}