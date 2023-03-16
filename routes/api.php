<?php

use App\Http\Controllers\Auth\GetAuthenticatedUserController;
use App\Http\Controllers\Auth\HandleLoginController;
use App\Http\Controllers\Auth\HandleLogoutController;
use App\Http\Controllers\Auth\HandleUserActivationController;
use App\Http\Controllers\Dashboard\GetDashboardDataController;
use App\Http\Middleware\EnsureUserIsActivated;
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

Route::middleware('guest')->group(function () {
    Route::post('/login', HandleLoginController::class)->name('login.handle');
});

Route::middleware('auth')->group(function () {
    Route::patch('/user-activation', HandleUserActivationController::class)->name('user.activation.handle');
    Route::post('/logout', HandleLogoutController::class)->name('logout.handle');

    Route::get('/authenticated-user', GetAuthenticatedUserController::class)->name('user.authenticated');

    Route::middleware(EnsureUserIsActivated::class)->group(function () {
        Route::get('/dashboard-data', GetDashboardDataController::class)->name('dashboard.data');
    });
});
