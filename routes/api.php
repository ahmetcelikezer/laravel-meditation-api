<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
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

Route::prefix('v1')->group(static function () {
    Route::prefix('auth')->group(static function () {
        Route::post('/register', [RegisterController::class, 'handle'])->middleware('without_token');
        Route::post('/login', [LoginController::class, 'handle'])->middleware('without_token');
    });
});
