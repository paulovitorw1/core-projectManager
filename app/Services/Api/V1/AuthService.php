<?php

namespace App\Services\Api\V1;

use App\Interfaces\Repositories\AuthRepositoryInterface;
use App\Interfaces\Services\OTPServiceInterface;
use App\Repositories\V1\AuthRepository;
use App\Interfaces\Services\AuthServiceInterface;
use App\Interfaces\Services\VerificationCodesServiceInterface;

class AuthService implements AuthServiceInterface
{
    /**
     * @var AuthRepositoryInterface $authRepository The AuthRepository instance to interact with the user data.
     */
    protected $authRepository;

    /**
     * @var VerificationCodesServiceInterface $verificationCodeService The otpService instance to interact with generate otp code.
     */
    protected $verificationCodeService;


    /**
     * AuthManager constructor.
     *
     * @param AuthRepository $repository The instance of AuthRepository to be used for authentication tasks.
     */
    public function __construct(
        AuthRepositoryInterface $repository = new AuthRepository(),
        VerificationCodesServiceInterface $verificationCodeService = new VerificationCodesService()
    ) {
        $this->authRepository = $repository;
        $this->verificationCodeService = $verificationCodeService;
    }

    /**
     * Create a new user.
     *
     * @param array $user The user data to be created.
     * @return mixed The result of the user creation operation (e.g., an ID or boolean value).
     */
    public function create(array $user)
    {
        $user = $this->authRepository->create($user);
        $this->verificationCodeService->create($user);
        return $user;
    }

    /**
     * Get the authenticated user from the provided token.
     *
     * @param string $token The token associated with the authenticated user.
     * @return mixed|null The authenticated user object or null if the token is invalid or user not found.
     */
    public function getUser($token)
    {
        return auth('api')->user();
    }

    /**
     * Attempt to log in the user with the provided credentials.
     *
     * @param array $credentials The user credentials (e.g., email/username and password) for login.
     * @return mixed The response data, typically including the user information and an authentication token.
     * @throws \Exception Thrown if login attempt fails, indicating unauthorized access.
     */
    public function login($credentials)
    {
        if (!$token = auth('api')->attempt($credentials)) {
            throw new \Exception('Unauthorized', 401);
        }

        return $this->respondWithToken($token)->getData();
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(): void
    {
        auth('api')->logout();
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Validate the OTP.
     * @param array $data contain otp for validate and userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateOTP(array $data)
    {
        return $this->verificationCodeService->validateOTP($data);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     * @param  bool $userVisible
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken(string $token, bool $userVisible = true)
    {
        $response = [
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'access_token' => $token,
        ];

        if ($userVisible) {
            $response['user'] = auth('api')->user();
        }

        return response()->json($response);
    }
}
