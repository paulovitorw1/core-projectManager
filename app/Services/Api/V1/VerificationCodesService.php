<?php

namespace App\Services\Api\V1;

use App\Interfaces\Repositories\VerificationCodesRepositoryInterface;
use App\Interfaces\Repositories\AuthRepositoryInterface;
use App\Interfaces\Services\OTPServiceInterface;
use App\Interfaces\Services\VerificationCodesServiceInterface;
use App\Mail\VerifyAccount;
use App\Models\User;
use App\Repositories\V1\AuthRepository;
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
     * @var AuthRepositoryInterface $authRepository The AuthRepository instance to interact with the user data.
     */
    protected $authRepository;
    protected $authService;

    /**
     * AuthManager constructor.
     *
     * @param VerificationCodesRepositoryInterface $repository The instance of AuthRepository to be used for authentication tasks.
     */
    public function __construct(
        VerificationCodesRepositoryInterface $repository = new VerificationCodesRepository(),
        OTPServiceInterface $otpService = new OTPService(),
        AuthRepositoryInterface $authRepository = new AuthRepository()
    ) {
        $this->repository = $repository;
        $this->otpService = $otpService;
        $this->authRepository = $authRepository;
    }

    /**
     * Create a new OTP code.
     *
     * @param User $user The user data for create the OTP.
     */
    public function create(array $user)
    {
        $user = $this->getUser($user['email']);
        $data = [
            'user_id' => $user->id,
            'otp' => $this->otpService->generateOTP(),
            'expire_at' => now()->addMinutes(10)
        ];

        $this->repository->create($data);
        $this->sendVerifyEmail($user['email'], $data['otp']);
    }

    /**
     * Check if the is valid OTP code.
     *
     * @param array $data The user data for create the OTP.
     * @return string
     */
    public function validateOTP(array $data)
    {
        $data['user_id'] = $this->getUser($data['email'])->id;
        return $this->repository->validateOTP($data);
    }

    /**
     * Send verify email for account validate
     *
     */
    public function sendVerifyEmail(string $email, string $otp)
    {
        Mail::to($email)->send(new VerifyAccount($otp));
    }

    /**
     * Get the user by email
     *     
     * @param string $email email user
     */
    public function getUser(string $email): User | null
    {
        return $this->authRepository->getUserByIdOrCredentials($email);
    }
}
