<?php

use App\Http\Controllers\AuthController;
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

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');

/**
 * User Authenticated routes
 */
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('user', [AuthController::class, 'user'])->name('user');
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
});
