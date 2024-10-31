<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\OTPController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\AuthenticateController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\WalletController;


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

Route::middleware('auth:sanctum')->group(function () {

    // Authentication Route
    Route::post('logout', [LoginController::class, 'logout']);
    Route::get('user', [AuthenticateController::class, 'loginUser']);

    // Services API Route
    Route::get('services', [ServiceController::class, 'listService']);
    Route::get('services/{identifier}', [ServiceController::class, 'listServiceProvider']);
    Route::get('services/variation/{service}', [ServiceController::class, 'listServiceVariation']);
    Route::post('services/pay', [ServiceController::class, 'payService']);
    Route::post('services/verify-merchant', [ServiceController::class, 'verifyMerchant']);


    // Wallet API Route
    Route::post('/wallet/deposit', [WalletController::class, 'deposit']);
    Route::get('/wallet/transactions', [WalletController::class, 'getTransactions']);
    Route::get('/wallet/balance', [WalletController::class, 'getBalance']);
    Route::get('/wallet/transaction/{id}', [WalletController::class, 'viewTransaction']);

});

Route::post('register', [RegisterController::class, 'store']);
Route::post('resend/otp', [OTPController::class, 'resendOTP']);
Route::post('verify/otp', [OTPController::class, 'verifyOTP']);
Route::post('login', [LoginController::class, 'login']);
Route::post('/reset/password', [ResetPasswordController::class, 'resetPassword']);
