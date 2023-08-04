<?php

namespace App\Repositories\V1;

use App\Interfaces\Repositories\VerificationCodesRepositoryInterface;
use App\Models\VerificationCode;

class VerificationCodesRepository implements VerificationCodesRepositoryInterface {
    protected $verificationCodeModel;

    public function __construct(VerificationCode $verificationCodeModel = new VerificationCode()) {
        $this->verificationCodeModel = $verificationCodeModel;
    }
    public function create(array $data): VerificationCode {
        return $this->verificationCodeModel->createValidOTP($data['user_id'], $data['otp'], $data['expireAt']);
    }

    // public function isValid(string $otp): VerificationCode {
        
    // }

    // public function delete(string $otp): VerificationCode {

    // }
}