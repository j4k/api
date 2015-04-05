<?php

namespace j4k\Api\Controller;

use BadMethodCallException;
use Illuminate\Routing\ControllerDispatcher as IlluminateControllerDispatcher;

class ControllerDispatcher extends IlluminateControllerDispatcher
{
    protected function makeController($controller)
    {
        $instance = parent::makeController($controller);

        $this->injectDependencies($instance);

        return $instance;
    }

    protected function injectDependencies($instance)
    {
        try {
            $instance->setResponseFactory($this->container['api.response']);
        } catch (BadMethodCallException $exception) {
            // This controller does not utilize the trait.
        }
    }
}