<?php namespace Eilander\Api\Providers;

use Illuminate\Http\Response;
use Illuminate\Support\ServiceProvider;

class RequestLoggerServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../Config/request-logger.php' => config_path('request-logger.php'),
        ]);
        $this->mergeConfigFrom(
            __DIR__.'/../Config/request-logger.php', 'request-logger'
        );
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // add middleware 
        $kernel = $this->app->make('Illuminate\Contracts\Http\Kernel');
        $kernel->prependMiddleware(\Eilander\RequestLogger\Http\Middleware\RequestLoggerMiddleware::class);
        
    }

}
