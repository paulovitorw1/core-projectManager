<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OTPValidationStatus;
use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRulesRequest;
use App\Http\Requests\RegisterUserRulesRequest;
use App\Http\Requests\ValidateOTPRulesRequest;
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
    public function create(RegisterUserRulesRequest $request, AuthService $authService)
    {
        try {
            $credentialsUser = $request->only(['name', 'email', 'password']);
            $user = $authService->create($credentialsUser);
            return Response::json($user, "", 201);
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
    public function login(LoginRulesRequest $request, AuthService $authService)
    {
        try {
            $credentials = $request->only(['email', 'password']);
            $auth = $authService->login($credentials);
            return Response::json($auth, "", 200);
        } catch (\Exception $e) {
            return Response::exception($e, "", $code = 404);
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

    /**
     * Validate OTP code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateOTP(ValidateOTPRulesRequest $request, AuthService $authService)
    {
        try {
            $data = $request->only(['otp', 'userId']);
            $validateOTP = $authService->validateOTP($data);
            switch ($validateOTP) {
                case OTPValidationStatus::VALID:
                    return Response::json(null, __('Your account has been successfully verified.'), 200);
                case OTPValidationStatus::INVALID:
                    throw new \Exception(__('The verification code is invalid.'));
                case OTPValidationStatus::EXPIRED:
                    throw new \Exception(__('The verification code has expired.'));
                case OTPValidationStatus::USED:
                    throw new \Exception(__('The verification code has already been used.'));
                default:
                throw new \Exception(__('The verification code is invalid.'));
            }
        } catch (\Exception $e) {
            return Response::exception($e, $e->getMessage(), 422);
        }
    }
}
