<?php

namespace App\Services\Api\V1;

use App\Enums\AuthUserStatus;
use App\Interfaces\Repositories\AuthRepositoryInterface;
use App\Interfaces\Services\OTPServiceInterface;
use App\Repositories\V1\AuthRepository;
use App\Interfaces\Services\AuthServiceInterface;
use App\Interfaces\Services\VerificationCodesServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
        $responseUser = $this->authRepository->create($user)->only(['id', 'email', 'name']);
        $this->verificationCodeService->create($responseUser);
        return $responseUser;
    }

    /**
     * Get the authenticated user from the provided token.
     *
     * @param string $token The token associated with the authenticated user.
     * @return mixed|null The authenticated user object or null if the token is invalid or user not found.
     */
    public function getUser($token = null)
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

        $user = $this->checkCredentials($credentials);
        if (!$user) {
            throw new \Exception(AuthUserStatus::NOT_FOUND, 401);
        }

        if (!$this->userIsActive($user)) {
            throw new \Exception(AuthUserStatus::DISABLED, 401);
        }

        if (!$this->userIsVerified($user)) {
            throw new \Exception(AuthUserStatus::NOT_VERIFIED, 401);
        }

        $token = auth('api')->attempt($credentials);

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
     * Send email with OTP code.
     * @param array $data contain otp for validate and userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendEmailWithOTP(array $data)
    {
        return $this->verificationCodeService->create($data);
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

    /**
     * Get user credentials based on the provided identifier (user ID or email).
     *
     * This method retrieves user credentials either by user ID or email, allowing retrieval
     * even when the user is not logged in.
     *
     * @param array|string $credentials The user credentials, which can be an array containing
     *                                 email and password or a user ID.
     *
     * @return User|null The User model instance if found, or null if not found.
     */
    public function getUserCredentials($credentials): User | null
    {
        return $this->authRepository->getUserByIdOrCredentials($credentials);
    }

    /**
     * Check if the user is verified.
     *
     * @param User $user The user instance to check for verification.
     *
     * @return bool Returns true if the user is verified, otherwise false.
     */
    private function userIsVerified($user)
    {
        if ($user instanceof User && $user->email_verified_at !== null) {
            return true;
        }

        return false;
    }

    /**
     * Check if the user is active.
     *
     * @param User $user The user instance to check for activity.
     *
     * @return bool Returns true if the user is active, otherwise false.
     */
    private function userIsActive($user)
    {
        if ($user instanceof User && $user->deleted_at !== null) {
            return false;
        }
        return true;
    }

    /**
     * Check if credentials is valid
     *
     * @param array $credentials The user credentials (e.g., email and password) for login.
     *
     * @return User | bool
     */

    private function checkCredentials($credentials)
    {
        $user = $this->authRepository->getUserByIdOrCredentials($credentials['email']);
        if ($user && Hash::check($credentials['password'], $user->password)) {
            return $user;
        }
        return false;
    }
}
