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

    private $exludedStatus = [
        404
    ];

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
        if (config('request-logger.log.enabled', false) && $this->shouldLog($request)) {
            // run job
            $this->dispatch(new RequestLogger($this->format($request, $response)));
        }
    }

    /**
     * Determine if the request has a URI that should be logged.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function shouldLog($request)
    {
        $only = config('request-logger.only');
        $except = config('request-logger.except');
        $methods = config('request-logger.methods');

        // first blacklist
        if ($this->requestIs($except, $request)) {
            return false;
        }

        // then whitelist
        if ($this->requestIs($only, $request)) {
            return true;
        }

        // methods
        if ($this->requestIsOfMethod($methods, $request)) {
            return true;
        }

        return false;
    }

    private function requestIs($list, $request)
    {
        if (is_array($list)) {
            foreach ($list as $except) {
                if ($except !== '/') {
                    $except = trim($except, '/');
                }

                if ($request->is($except)) {
                    return true;
                }
            }
        }
        return false;
    }

     private function requestIsOfMethod($list, $request)
    {
        if (is_array($list)) {
            if (in_array($request->method(), $list)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determine if the request has a URI that should not be logger.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function shouldNotLog($request, $excepts)
    {
        foreach ($excepts as $except) {
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
    private function format($request, $response)
    {
        $formatter = config('request-logger.format.default');
        if ($this->shouldFormatAsExcept($request, config('request-logger.except-format'))) {
            $formatter = config('request-logger.format.except');
        }

        $replacements = [
            'status'   => $response->status(),
            'content'  => $response->content(),
            'method'   => $request->method(),
            'body'     => json_encode($request->all()),
            'full-url' => $request->fullUrl(),
            'time'     => round($this->time() * 1000, 0) . ' ms'
        ];
        return $this->formatter($replacements, config('request-logger.log.format.'.$formatter));
    }

    /**
     * Formar log
     *
     * @param  array $replacements
     * @return bool
     */
    private function formatter($replacements, $format)
    {
        foreach($replacements AS $key => $value)
        {
        	$format = str_replace('{'.$key.'}', $value, $format);
        }

        return $format;
    }
}