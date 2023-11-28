<?php

namespace App\Repositories\V1;

use App\Interfaces\Repositories\VerificationCodesRepositoryInterface;
use App\Models\VerificationCode;
use App\Enums\OTPValidationStatus;

class VerificationCodesRepository implements VerificationCodesRepositoryInterface
{
    protected $verificationCodeModel;

    public function __construct(VerificationCode $verificationCodeModel = new VerificationCode())
    {
        $this->verificationCodeModel = $verificationCodeModel;
    }

    public function create(array $data): VerificationCode
    {
        $this->invalidateExistingCodes($data['user_id']);
        return $this->verificationCodeModel->create([
            'user_id' => $data['user_id'],
            'otp' => $data['otp'],
            'expire_at' => $data['expire_at'],
            'status' => OTPValidationStatus::VALID,
        ]);
    }

    /**
     * Check if the is valid OTP code.
     *
     * @param array $data The user data for validation the OTP.
     * @return VerificationCode | null The return is VerificationCode
     */
    public function validateOTP($data): string
    {
        return $this->verificationCodeModel->validateOTP($data);
    }

    /**
     * Invalidate existing verification codes for a given user.
     *
     * This method updates the status of existing verification codes for the specified user
     * whose expiration time is greater than the current time, marking them as invalid.
     *
     * @param string $userId The ID of the user for whom to invalidate codes.
     * @return void
     */
    public function invalidateExistingCodes(string $userId): void
    {
        $this->verificationCodeModel
            ->where('user_id', $userId)
            ->where('expire_at', '>', now())
            ->update(['status' => OTPValidationStatus::INVALID]);
    }
}
