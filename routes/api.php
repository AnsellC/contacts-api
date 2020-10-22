<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactEntryController;
use App\Http\Controllers\MeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
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
Route::middleware('throttle:60,1')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::post('/login', [AuthController::class, 'login']);

});

Route::middleware(['auth:api', 'throttle:60,1'])->group(function () {
    Route::post('/me', [AuthController::class, 'me']);
    Route::apiResource('contacts', ContactController::class);
    Route::apiResource('contact_entries', ContactEntryController::class);
});
