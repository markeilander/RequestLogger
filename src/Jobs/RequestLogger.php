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

    private $request;
    private $response;
    private $time;
    private $format;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Request $request, Response $response, $time, $format)
    {
        $this->request = $request;
        $this->response = $response;
        $this->time = $time;
        $this->format = $format;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $replacements = [
            'status'   => $this->response->status(),
            'content'  => $this->response->content(),
            'method'   => $this->request->method(),
            'full-url' => $this->request->fullUrl(),
            'time'     => round($this->time * 1000, 0) . ' ms'
        ];
        Log::info($this->formatter($replacements));
    }

    /**
     * Formar log
     *
     * @param  array $replacements
     * @return bool
     */
    private function formatter($replacements)
    {
        foreach($replacements AS $key => $value)
        {
        	$this->format = str_replace('{'.$key.'}', $value, $this->format);
        }

        return $this->format;
    }
}
