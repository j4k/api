<?php

namespace j4k\Api\Provider;

use Illuminate\Support\ServiceProvider;
use j4k\Api\Http\ResponseFactory;
use j4k\Api\Transformer\TransformerFactory;
use j4k\Api\Controller\ControllerDispatcher;

class ApiServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->setUp();
    }

    protected function setUp()
    {
        // Set up API Configuration file
        $this->publishes([
            __DIR__.'../Config/config.php' => config_path('api.php'),
        ], 'config');
    }

    public function register()
    {
        $this->overloadControllerDispatcher();
        $this->registerTransformer();
        $this->registerResponseFactory();
        $this->setUpBinds();
    }

    protected function overloadControllerDispatcher()
    {
        $this->app->singleton('illuminate.route.dispatcher', function($app)
        {
            return new ControllerDispatcher($app['router'], $app);
        });
    }

    /**
     * Register the Transformer Factory
     * @return void
     */
    protected function registerTransformer()
    {
        $this->app->singleton('api.transformer', function ($app) {
            $transformer = $this->app->make('j4k\Api\Transformer\FractalTransformer');
            return new TransformerFactory($app, $transformer);
        });
    }

    /**
     * Register the API response factory.
     * @return void
     */
    protected function registerResponseFactory()
    {
        $this->app->singleton('api.response', function ($app) {
            return new ResponseFactory($app['api.transformer']);
        });
    }

    /**
     *
     */
    protected function setUpBinds()
    {
        $this->app->bind('j4k\Api\Http\ResponseFactory', function($app)
        {
            return $app['api.response'];
        });
    }

}
