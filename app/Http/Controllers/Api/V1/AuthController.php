<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRulesRequest;
use App\Interfaces\Services\AuthServiceInterface;
use App\Services\Api\V1\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Register new user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(RegisterUserRulesRequest $request, AuthService $authService) {
        try {
            $credentialsUser = $request->only(['name', 'email', 'password']);
            $user = $authService->create($credentialsUser);
            return Response::json($user, "", 200);
        } catch (\Exception $e) {
            return Response::exception($e);
        }
    }

    /**
     * Get authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser(AuthService $authService)
    {
        try {
            $user = $authService->getUser($token = "dasda");
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
    public function login(Request $request, AuthService $authService)
    {
        try {
            $credentials = $request->only(['email', 'password']);
            $auth = $authService->login($credentials);
            return Response::json($auth, "", 200);
        } catch (\Exception $e) {
            return Response::exception($e, "", $code = 401);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(AuthService $authService)
    {
        try {
            $authService->logout();
            return Response::json(null, "", 204);
        } catch (\Exception $e) {
            return Response::exception($e);
        }
    }
}