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
    Route::get('/testeEmail', function() {
        $otp = rand(1000,9999);
        
        // $name = "123123";
    
        // // The email sending is done using the to method on the Mail facade
        // Mail::to('paulovitor-100-@outlook.com')->send(new VerifyAccount($name));
    });
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/user/create', [AuthController::class, 'create']);
    
    Route::group(['middleware' => 'jwt-api', 'prefix' => 'auth'], function() {
        Route::get('/user', [AuthController::class, 'getUser']);
        Route::post('/logout', [AuthController::class, 'logout']);
    
    });
});
