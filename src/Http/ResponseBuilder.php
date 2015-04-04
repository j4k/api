<?php

namespace j4k\Api\Http;

use Response;
use Symfony\Component\HttpFoundation\Cookie;

class ResponseBuilder
{

    protected $response;

    protected $transformerBind;

    protected $headers = [];

    protected $cookies = [];

    protected $statusCodes = 200;

    public function __construct($response, $transformerBind)
    {
        $this->response = $response;
        $this->transformerBind = $transformerBind;
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
        $response = new Response($this->response, $this->statusCode, $this->headers);

        foreach ($this->cookies as $cookie) {
            if ($cookie instanceof Cookie)
                $response->withCookie($cookie);
        }

        return $response;
    }
}
