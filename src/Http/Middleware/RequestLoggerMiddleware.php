<?php 

namespace  Eilander\RequestLogger\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Symfony\Component\HttpFoundation\Response;
use Eilander\RequestLogger\Jobs\RequestLogger;
use Closure;

class RequestLoggerMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    /** 
     * Perform any final actions for the request lifecycle.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return void
     */
    public function terminate(Request $request, Response $response)
    {
        if (config('request-logger.log.enabled', false)) {
            $time = microtime(true) - LARAVEL_START;
            $this->dispatch(new RequestLogger($request, $response, $time));
        }
    }
}