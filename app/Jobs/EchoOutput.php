<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class EchoOutput implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected $message
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        throw new \Exception('This is a test exception');
        Log::info('Job sent at '.$this->message->format('Y-m-d H:i:s'));

    }
}
