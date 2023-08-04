<?php

namespace App\Services\Api\V1;

use App\Interfaces\Repositories\VerificationCodesRepositoryInterface;
use App\Interfaces\Services\OTPServiceInterface;
use App\Interfaces\Services\VerificationCodesServiceInterface;
use App\Mail\VerifyAccount;
use App\Models\User;
use App\Repositories\V1\VerificationCodesRepository;
use Illuminate\Support\Facades\Mail;

class VerificationCodesService implements VerificationCodesServiceInterface
{
    /**
     * @var VerificationCodesRepositoryInterface $repository The AuthRepository instance to interact with the user data.
     */
    protected $repository;

    /**
     * @var OTPServiceInterface $otpService The otpService instance to interact with generate otp code.
     */
    protected $otpService;
    /**
     * AuthManager constructor.
     *
     * @param VerificationCodesRepositoryInterface $repository The instance of AuthRepository to be used for authentication tasks.
     */
    public function __construct(
        VerificationCodesRepositoryInterface $repository = new VerificationCodesRepository(),
        OTPServiceInterface $otpService = new OTPService()
    ) {
        $this->repository = $repository;
        $this->otpService = $otpService;
    }

    /**
     * Create a new OTP code.
     *
     * @param User $user The user data for create the OTP.
     */
    public function create(User $user)
    {
        $data = [
            'user_id' => $user->id,
            'otp' => $this->otpService->generateOTP(),
            'expireAt' => now()->addMinutes(10)
        ];

        $this->repository->create($data);
        $this->sendVerifyEmail($user->email, $data['otp']);
    }

    //TODO: [WIP] Impment validation OTP
    /**
     * Check if the is valid OTP code.
     *
     * @param User $user The user data for create the OTP.
     */
    // public function isValid(string $otp): bool
    // {
    // }

    /**
     * Send verify email for account validate
     *
     */
    public function sendVerifyEmail(string $email, string $otp)
    {
        Mail::to($email)->send(new VerifyAccount($otp));
    }
}