<?php

use App\Http\Controllers\Auth\GetAuthenticatedUserController;
use App\Http\Controllers\Auth\GetAuthenticableUsersController;
use App\Http\Controllers\Auth\HandleLoginController;
use App\Http\Controllers\Auth\HandleLogoutController;
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
    Route::get('/users', GetAuthenticableUsersController::class)->name('users');
    Route::post('/login', HandleLoginController::class)->name('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/user', GetAuthenticatedUserController::class)->name('user');
    Route::post('/logout', HandleLogoutController::class)->name('logout');
});
