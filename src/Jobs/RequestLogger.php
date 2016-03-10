<?php

namespace Eilander\RequestLogger\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Log;

class RequestLogger extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $format;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($format)
    {
        $this->format = $format;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info($this->format);
    }
}
