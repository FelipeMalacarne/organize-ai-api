<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PubSubController;
use App\Http\Controllers\SocialMediaController;
use App\Http\Resources\UserResource;
use App\Jobs\EchoOutput;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return UserResource::make($request->user()->load('socialAccounts'));
})->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('login/{provider}', [SocialMediaController::class, 'redirectToProvider']);
Route::get('login/{provider}/callback', [SocialMediaController::class, 'handleProviderCallback']);

Route::middleware('auth:sanctum')->group(function (){
    Route::apiResource('document', DocumentController::class);
    Route::get('document/{id}/download', [DocumentController::class, 'download']);

});


Route::get('queue', function () {
    EchoOutput::dispatch(Carbon::now());

    return response()->json(['message' => 'Job dispatched']);
});

Route::post('/jobs', [PubSubController::class, 'handle']);
