<?php

namespace j4k\Api\Http;

use Symfony\Component\HttpFoundation\Cookie;
use Illuminate\Http\Response;

class ResponseBuilder
{

    protected $meta = [];

    protected $response;

    protected $headers = [];

    protected $cookies = [];

    protected $statusCode = 200;

    public function __construct($response)
    {
        $this->response = $response;
    }

    public function addMeta($key, $val){
        $this->meta[$key] = $val;
        return $this;
    }

    public function meta($key, $val)
    {
        $this->addMeta($key, $val);
        return $this;
    }

    public function withCookie(Cookie $cookie)
    {
        $this->cookies[] = $cookie;
        return $this;
    }

    public function cookie(Cookie $cookie){
        return $this->withCookie($cookie);
    }

    public function withHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function header($name, $value)
    {
        return $this->withHeader($name, $value);
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function build()
    {
        $this->appendMeta();

        $response = new Response($this->response, $this->statusCode, $this->headers);

        foreach ($this->cookies as $cookie) {
            if ($cookie instanceof Cookie)
                $response->withCookie($cookie);
        }

        return $response;
    }
    public function appendMeta()
    {
        foreach($this->meta as $key => $val){
            $this->response['meta'][$key] = $val;
        }
    }

}

