<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/v1')->group(function () {
    Route::prefix('/auth')->group(function () {
        Route::controller(AuthController::class)->group(function () {
            Route::get('/unauthorized', 'unauthorized')->name('unauthorized');
            Route::post('/register', 'createUser')->middleware('prevent.user.duplicate');
            Route::post('/verify-otp', 'verifyOtp');
            Route::post('/resend-otp', 'resendOtp');
            Route::post('/login', 'login');
            Route::post('/logout', 'logout')->middleware('auth:sanctum');
            Route::post('/password/email', 'sendResetPassword');
        });
    });
});