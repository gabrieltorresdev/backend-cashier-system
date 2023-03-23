<?php

use App\Http\Controllers\Auth\GetAuthenticatedUserController;
use App\Http\Controllers\Auth\HandleLoginController;
use App\Http\Controllers\Auth\HandleLogoutController;
use App\Http\Controllers\Auth\HandleUserActivationController;
use App\Http\Controllers\Dashboard\GetDashboardDataController;
use App\Http\Middleware\EnsureUserIsActivated;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{
    GetAuthenticatedUserController,
    HandleLoginController,
    HandleLogoutController,
    HandleUserActivationController
};
use App\Http\Controllers\Dashboard\{
    GetCurrentOpenedCashRegisterController,
    OpenCashRegisterController,
    CloseCashRegisterController,
    GetCashRegisterTransactionsController,
    UpdateTransactionController
};

Route::middleware('guest')->group(function () {
    Route::post('/login', HandleLoginController::class);
});

Route::middleware('auth')->group(function () {
    Route::patch('/user-activation', HandleUserActivationController::class);
    Route::post('/logout', HandleLogoutController::class);

    Route::get('/authenticated-user', GetAuthenticatedUserController::class);

    Route::middleware(EnsureUserIsActivated::class)->group(function () {
        Route::get('/opened-cash-register', GetCurrentOpenedCashRegisterController::class);
        Route::get('/transactions', GetCashRegisterTransactionsController::class);
        Route::put('/transactions', UpdateTransactionController::class)->middleware('throttle:1,0.18');
        Route::patch('/cash-register/close', CloseCashRegisterController::class);
        Route::patch('/cash-register/open', OpenCashRegisterController::class);
    });
});

// TODO: Create transaction
// TODO: Add products to transaction
// TODO: List all products in stock
// TODO: Admin user registration
