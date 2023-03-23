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

Route::middleware('guest')->group(function () {
    Route::post('/login', HandleLoginController::class);
});

Route::middleware('auth')->group(function () {
    Route::patch('/user-activation', HandleUserActivationController::class);
    Route::post('/logout', HandleLogoutController::class);

    Route::get('/authenticated-user', GetAuthenticatedUserController::class);

    Route::middleware(EnsureUserIsActivated::class)->group(function () {
        Route::get('/dashboard', GetDashboardDataController::class)->name('dashboard');
    });
});

// TODO: Create transaction
// TODO: Add products to transaction
// TODO: List all products in stock
// TODO: Admin user registration
