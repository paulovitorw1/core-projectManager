<?php

namespace App\Interfaces\Services;

interface OTPServiceInterface
{
    public function generateOTP($length = 6): string;
}