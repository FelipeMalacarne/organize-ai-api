<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialMediaController;
use App\Jobs\EchoOutput;
use Carbon\Carbon;
use Google\Cloud\PubSub\Message;
use Google\Cloud\PubSub\PubSubClient;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Kainxspirits\PubSubQueue\Jobs\PubSubJob;
use Kainxspirits\PubSubQueue\PubSubQueue;

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

    $pubSubMessage = json_decode($request->getContent(), true);

    Log::debug('Pub/Sub Message: ', [$pubSubMessage]);

    if (isset($pubSubMessage['message']['data'])) {
        $decoded = base64_decode(base64_decode(Arr::get($pubSubMessage, 'message.data')));

        Log::debug('Decoded Pub/Sub Message: ', [$decoded]);

        $jobData = json_decode($decoded, true);

        if (isset($jobData['data']['command'])) {
            try {
                $queue = config('queue.connections.pubsub.queue');
                $maxTries = Arr::get($jobData, 'maxTries', config('queue.connections.pubsub.retries', 3));
                // Unserialize the command (this recreates the job object)
                // $job = unserialize($jobData['data']['command']);

                $pubSubMessage['message']['data'] = base64_decode($pubSubMessage['message']['data'], true);

                $message = new Message($pubSubMessage['message']);

                $job = new PubSubJob(
                    App::make(Container::class),
                    new PubSubQueue(
                        new PubSubClient([
                            'projectId' => config('queue.connections.pubsub.project_id'),
                            'keyFilePath' => config('queue.connections.pubsub.key_file_path'),
                        ]),
                        $queue
                    ),
                    $message,
                    'pubsub',
                    'dev-laravel-queue'
                );

                rescue(fn () => $job->fire(),
                    function (\Exception $e) use ($job) {
                        Log::error('Job processing failed: '.$e->getMessage());
                        if ($job->attempts() < $job->maxTries()) {
                            $job->release();
                        }
                        if ($job->attempts() >= $job->maxTries()) {
                            $job->fail($e);

                            DB::insert('insert into failed_jobs (uuid, connection, queue, payload, exception, failed_at) values (?, ?, ?, ?, ?, ?)', [
                                $job->getJobId(),
                                'pubsub',
                                'dev-laravel-queue',
                                $job->getRawBody(),
                                $e->getMessage(),
                                now(),
                            ]);
                        }

                        return response()->json(['status' => 'Job processing failed', 'error' => $e->getMessage()], 200);
                    });

                return response()->json(['status' => 'Job processed successfully'], 200);

            } catch (\Exception $e) {
                Log::error('Job processing failed: '.$e->getMessage());

                // Return non-200 status code to signal failure to Pub/Sub
                return response()->json(['status' => 'Job processing failed', 'error' => $e->getMessage()], 200);
            }
        }

        return response()->json(['status' => 'Invalid job data'], 400);
    }

    return response()->json(['status' => 'Invalid Pub/Sub message'], 400);
});
