<?php

namespace App\Interfaces\Repositories;

use App\Models\User;
use App\Models\VerificationCode;

interface VerificationCodesRepositoryInterface
{
    public function create(array $data): VerificationCode;
    // public function delete(string $otp): VerificationCode;
    // public function isValid(string $otp): VerificationCode;
}