<?php

namespace j4k\Api\Middleware;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Route;
use j4k\Api\Exceptions\InvalidHeaderException;
use j4k\Api\Exceptions\InvalidMediaExtensionRequest;
use j4k\Api\Http\ResponseFactory;

class ApiMiddleware
{

    /**
     * Container instance
     */
    protected $container;

    /**
     * @param Api Version
     */
    protected $version;

    /**
     * @param Format
     */
    protected $format;

    /**
     * @var
     */
    protected $factory;

    /**
     * Array of Supported JSON API Extensions
     * Added in as headers
     * @var array
     */
    protected $supportedExtensions = [];

    /**
     * @param ResponseFactory $factory
     */
    public function __construct(Container $container, ResponseFactory $response)
    {
        $this->container = $container;
        $this->factory = $response;
    }

    /**
     * Handle a request
     * @return string Returns the phrase passed in
     */
    public function handle($request, Closure $next)
    {
        try {
            list($format, $version) = $this->parseAcceptHeader($request);
            $this->version = $version;
            $this->currentFormat = $format;

            $this->parseRequestedExtensions();
        } catch ( InvalidHeaderException $e) {
            return $this->factory->errorNotSupported('Unsupported Media Type.')->build();
        } catch ( InvalidMediaExtensionRequest $e) {
            return $this->factory->errorNotAcceptable()->build();
        }

        $response = $next( $request );

        // do things with response
        return $response;
    }

    /**
     * @param Request $request
     * @return array
     * @throws InvalidHeaderException
     */
    public function parseAcceptHeader(Request $request)
    {
        if (preg_match('#application\/vnd\.api(?:\.(v[\d\.]+))*\+(\w+)#',
            $request->header('accept'), $matches )){
            return array_slice($matches, 1);
        } elseif ($this->isStrict()) {
            // then we should throw
            throw new InvalidHeaderException();
        }
        // TODO : Config
        return ['v1.0', 'json'];
    }

    public function parseRequestedExtensions()
    {
        // TODO : check that the requested extensions are things the API can cater for
    }

    private function isStrict()
    {
        return $this->container['config']['api.strict'];
    }

}
