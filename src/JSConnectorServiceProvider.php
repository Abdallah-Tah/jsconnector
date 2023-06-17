<?php

namespace Amohamed\JSConnector;

use Illuminate\Support\ServiceProvider;
use Amohamed\JSConnector\JSConnectorService;
use Amohamed\JSConnector\Console\Commands\JsConnectorServeCommand;

class JSConnectorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('JSConnector', function ($app) {
            return new JSConnectorService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/jsconnector.php' => config_path('jsconnector.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                JsConnectorServeCommand::class
            ]);
        }
    }
}
