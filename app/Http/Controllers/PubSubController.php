<?php

namespace App\Http\Controllers;

use App\Http\Requests\PubSubEventRequest;
use App\Services\Pubsub\JobProcessor;
use App\Services\Pubsub\PubSubEvent;

class PubSubController extends Controller
{
    public function handle(PubSubEventRequest $request, PubSubEvent $event, JobProcessor $processor)
    {
        $job = $event->setMessage($request->getPubSubMessage())->createJob();

        $processor->setJob($job)->handle();

        return response()->json(['status' => 'Job processed successfully'], 200);
    }
}
