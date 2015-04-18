<?php

namespace j4k\Api\Middleware;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Route;

class ApiMiddlewareClass
{

    /**
     * @param Container
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
     * Accept Headers accepted
     */
    protected $acceptedContentTypes = [
        'application/vnd.api+json',
        'application/json'
    ];

    /**
     * Array of Supported JSON API Extensions
     * @var array
     */
    protected $supportedExtensions = [];

    /**
     * Handle a request
     *
     * @param string $phrase Phrase to return
     *
     * @return string Returns the phrase passed in
     */
    public function handle($request, Closure $next)
    {
        $this->container = new Container;

        list($version, $format) = $this->parseAcceptHeader($request);

        $this->version = $version;
        $this->currentFormat = $format;

        $route = app()->router->getCurrentRoute();

        return $next( $request );
    }

    /**
     * Get the request headers
     *
     * @param Request $request Request Object
     *
     * @return array Returns headers
     */
    public function parseAcceptHeader(Request $request)
    {
        return ['1.0' , 'json'];
        Request::format();
//        /**
//         * Gets a list of content types acceptable by the client browser.
//         *
//         * @return array List of content types in preferable order
//         *
//         * @api
//         */
//        public function getAcceptableContentTypes()
//    {
//        if (null !== $this->acceptableContentTypes) {
//            return $this->acceptableContentTypes;
//        }
//
//        return $this->acceptableContentTypes = array_keys(AcceptHeader::fromString($this->headers->get('Accept'))->all());
//    }

    }

}
