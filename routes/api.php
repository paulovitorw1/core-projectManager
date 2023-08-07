<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Mail\VerifyAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::post('/validate/otp', [AuthController::class, 'validateOTP']);
    Route::post('/sendEmail/otp', [AuthController::class, 'sendEmailWithOTP']);
    
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/user/create', [AuthController::class, 'create']);
    
    Route::group(['middleware' => 'jwt-api', 'prefix' => 'auth'], function() {
        Route::get('/user', [AuthController::class, 'getUser']);
        Route::post('/logout', [AuthController::class, 'logout']);
    
    });
});
