<?php
namespace App\Services\Api\V1;

use App\Repositories\V1\AuthRepository;
use App\Interfaces\Services\AuthServiceInterface;

class AuthService implements AuthServiceInterface
{
    /**
     * @var AuthRepository $authRepository The AuthRepository instance to interact with the user data.
     */
    protected $authRepository;

    /**
     * AuthManager constructor.
     *
     * @param AuthRepository $repository The instance of AuthRepository to be used for authentication tasks.
     */
    public function __construct(AuthRepository $repository)
    {
        $this->authRepository = $repository;
    }

    /**
     * Create a new user.
     *
     * @param array $user The user data to be created.
     * @return mixed The result of the user creation operation (e.g., an ID or boolean value).
     */
    public function create($user)
    {
        return $this->authRepository->create($user);
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
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'user' => auth('api')->user(),
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'access_token' => $token,
        ]);
    }
}