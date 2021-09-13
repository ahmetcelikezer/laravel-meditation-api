<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Meditation\MeditationController;
use App\Http\Controllers\Report\ReportController;
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
        Route::post('/logout', [LogoutController::class, 'handle'])->middleware('auth:sanctum');
    });

    Route::middleware('auth:sanctum')->group(static function () {
        Route::prefix('meditation')->group(static function () {
            Route::post('/complete/{meditation}', [MeditationController::class, 'completeAction']);
            Route::get('/report/{reportType}', [ReportController::class, 'createReport']);
        });
    });
});
