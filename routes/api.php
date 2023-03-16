<?php

use App\Http\Controllers\Auth\GetAuthenticatedUserController;
use App\Http\Controllers\Auth\HandleLoginController;
use App\Http\Controllers\Auth\HandleLogoutController;
use App\Http\Controllers\Dashboard\GetDashboardDataController;
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
    Route::post('/handle-login', HandleLoginController::class)->name('login.handle');
});

Route::middleware('auth')->group(function () {
    Route::get('/get-authenticated-user', GetAuthenticatedUserController::class)->name('user.authenticated');
    Route::get('/get-dashboard-data', GetDashboardDataController::class)->name('dashboard.data');
    Route::post('/handle-logout', HandleLogoutController::class)->name('logout.handle');
});
