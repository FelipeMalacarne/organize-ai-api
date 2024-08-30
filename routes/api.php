<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialMediaController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('login/{provider}', [SocialMediaController::class, 'redirectToProvider']);
Route::get('login/{provider}/callback', [SocialMediaController::class, 'handleProviderCallback']);
