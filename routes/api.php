<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialMediaController;
use App\Jobs\EchoOutput;
use App\Services\Pubsub\Message;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('login/{provider}', [SocialMediaController::class, 'redirectToProvider']);
Route::get('login/{provider}/callback', [SocialMediaController::class, 'handleProviderCallback']);

Route::get('test', fn () => response()->json(['message' => 'hello world']));

Route::get('queue', function () {
    EchoOutput::dispatch(Carbon::now());

    return response()->json(['message' => 'Job dispatched']);
});

Route::post('/jobs', function (Request $request) {
    // Decode the incoming Pub/Sub message
    $pubSubMessage = json_decode($request->getContent(), true);

    // Ensure the message is valid and has a 'data' field
    if (isset($pubSubMessage['message']['data'])) {
        // First Base64 decode
        $decodedOnce = base64_decode($pubSubMessage['message']['data']);

        // Second Base64 decode
        $decodedTwice = base64_decode($decodedOnce);

        // Log the decoded message for debugging
        Log::debug('Decoded Pub/Sub Message: ', [$decodedTwice]);

        // Unserialize the job payload (this recreates the original Job object)
        $jobData = json_decode($decodedTwice, true);

        if (isset($jobData['data']['command'])) {
            try {
                // Unserialize the command (this recreates the job object)
                $job = unserialize($jobData['data']['command']);

                // Process the job by calling its handle method directly
                $job->handle();

                return response()->json(['status' => 'Job processed successfully'], 200);

            } catch (\Exception $e) {
                Log::error('Job processing failed: '.$e->getMessage());

                // Return non-200 status code to signal failure to Pub/Sub
                return response()->json(['status' => 'Job processing failed', 'error' => $e->getMessage()], 500);
            }
        }

        return response()->json(['status' => 'Invalid job data'], 400);
    }

    return response()->json(['status' => 'Invalid Pub/Sub message'], 400);
});
