<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Interfaces\Services\AuthServiceInterface;
use App\Http\Services\Api\V1\AuthService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private $authService;
    public function __construct(AuthServiceInterface $authService = new AuthService())
    {
        $this->authService = $authService;
    }
    public function getUser()
    {
        try {
            $user = $this->authService->getUser($token = "dasda");
            return Response::json($user, "", 200);
        } catch (\Exception $e) {
            return Response::exception($e);
        }
    }
    
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->only(['email', 'password']);
            $auth = $this->authService->login($credentials);
            return Response::json($auth, "", 200);
        } catch (\Exception $e) {
            return Response::exception($e);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            $this->authService->logout();
            return Response::json(null, "", 204);
        } catch (\Exception $e) {
            return Response::exception($e);
        }
    }
}