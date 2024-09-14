<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class EchoOutput implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected \Carbon\Carbon $message
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Job sent at '.$this->message->format('Y-m-d H:i:s'));
    }
}
