<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\FilmController;
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
 * Authenticated routes
 */
Route::group(['middleware' => ['auth:sanctum']], function () {
    /**
     * User
     */
    Route::get('user', [AuthController::class, 'user'])->name('user');
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    /**
     * Film
     */
    Route::resource('films', FilmController::class)->only(['store', 'destroy']);
    Route::post('comment', [FilmController::class, 'comment'])->name('comment');
    Route::get('countryList', [CountryController::class, 'countryList'])->name('countryList');
});

/**
 * Guest routes Film
 */
Route::resource('films', FilmController::class)
    ->only(['index', 'show'])
    ->parameters([
        'films' => 'slug'
    ]);
