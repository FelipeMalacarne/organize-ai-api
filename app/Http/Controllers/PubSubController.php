<?php

namespace App\Http\Controllers;

use App\Http\Requests\PubSubEventRequest;
use App\Services\Pubsub\PubSubEvent;

class PubSubController extends Controller
{
    public function handle(PubSubEventRequest $request, PubSubEvent $event)
    {
        $event->setMessage($request->getPubSubMessage())->handle();

        return response()->json(['status' => 'Job processed successfully'], 200);
    }
}
