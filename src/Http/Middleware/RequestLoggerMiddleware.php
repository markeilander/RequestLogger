<?php

namespace  Eilander\RequestLogger\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Symfony\Component\HttpFoundation\Response;
use Eilander\RequestLogger\Jobs\RequestLogger;
use Closure;

class RequestLoggerMiddleware
{
    use DispatchesJobs;

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
        if (config('request-logger.log.enabled', false) && $this->shouldPassThrough($request, config('request-logger.except'))) {
            // run job
            $this->dispatch(new RequestLogger($request, $response, $this->time(), $this->format($request)));
        }
    }

    /**
     * Determine if the request has a URI that should pass through CSRF verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function shouldPassThrough($request, $excepts)
    {
        foreach ($excepts as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->is($except)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the request has a URI that should pass through CSRF verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    private function shouldFormatAsExcept($request, $exceptFormat)
    {
        foreach ($exceptFormat as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine wthe time spend
     */
    private function time()
    {
       return microtime(true) - LARAVEL_START;
    }

    /**
     * Determine which formatter to use
     */
    private function format($request)
    {
        $formatter = config('request-logger.format.default');
        if ($this->shouldFormatAsExcept($request, config('request-logger.except-format'))) {
            $formatter = config('request-logger.format.except');
        }
        return config('request-logger.log.format.'.$formatter);
    }
}