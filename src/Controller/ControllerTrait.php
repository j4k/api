<?php

namespace j4k\Api\Controller;

use App;
use j4k\Api\Http\ResponseFactory;

trait ControllerTrait
{

    protected $response;

    public function setResponseFactory(ResponseFactory $response)
    {
        $this->response = $response;
    }

    public function __call($method, $parameters)
    {
        if (method_exists($this->response, $method)) {
            return call_user_func_array([$this->response, $method], $parameters);
        }
        return parent::__call($method, $parameters);
    }

}