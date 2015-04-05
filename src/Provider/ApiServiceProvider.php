<?php

namespace j4k\Api\Provider;

use Illuminate\Support\ServiceProvider;
use j4k\Api\Http\ResponseFactory;
use j4k\Api\Transformer\TransformerFactory;

class ApiServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->setUp();
    }

    protected function setUp()
    {

    }

    public function register()
    {
        $this->registerTransformer();
        $this->registerResponseFactory();
    }

    /**
     * Register the Transformer Factory
     * @return void
     */
    protected function registerTransformer()
    {
        $this->app->bindShared('api.transformer', function ($app) {
            // TODO : make config obj
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
        $this->app->bindShared('api.response', function ($app) {
            return new ResponseFactory($app['api.transformer']);
        });
    }


}
