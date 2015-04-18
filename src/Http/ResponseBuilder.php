<?php

namespace j4k\Api\Http;

use Symfony\Component\HttpFoundation\Cookie;
use Illuminate\Http\Response;

class ResponseBuilder
{
    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @var
     */
    protected $response;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var array
     */
    protected $cookies = [];

    /**
     * @var int
     */
    protected $statusCode = 200;

    /**
     * @param $response
     */
    public function __construct($response)
    {
        $this->response = $response;
    }

    /**
     * @param $key
     * @param $val
     * @return $this
     */
    public function addMeta($key, $val){
        $this->meta[$key] = $val;
        return $this;
    }

    /**
     * @param $key
     * @param $val
     * @return $this
     */
    public function meta($key, $val)
    {
        $this->addMeta($key, $val);
        return $this;
    }

    /**
     * @param Cookie $cookie
     * @return $this
     */
    public function withCookie(Cookie $cookie)
    {
        $this->cookies[] = $cookie;
        return $this;
    }

    /**
     * @param Cookie $cookie
     * @return ResponseBuilder
     */
    public function cookie(Cookie $cookie){
        return $this->withCookie($cookie);
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function withHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return ResponseBuilder
     */
    public function header($name, $value)
    {
        return $this->withHeader($name, $value);
    }

    /**
     * @param $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @return Response
     */
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

    /**
     *
     */
    protected function appendMeta()
    {
        foreach($this->meta as $key => $val){
            $this->response['meta'][$key] = $val;
        }
    }

}

