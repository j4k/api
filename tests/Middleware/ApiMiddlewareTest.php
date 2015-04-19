<?php

namespace j4k\Api\Test\Middleware;

use Mockery as m;
use j4k\Api\Middleware\ApiMiddleware;
use j4k\Api\Http\ResponseFactory;
use Illuminate\Container\Container as Container;

class ApiMiddlewareTest extends \PHPUnit_Framework_TestCase
{

    public function __construct()
    {
        $this->transformer = m::mock('j4k\Api\Transformer\TransformerFactory');
        $this->factory = new ResponseFactory($this->transformer);
    }

    public function setUp()
    {
        $this->container = new Container;
    }

    public function tearDown()
    {
        m::close();
    }

    public function testParsesAcceptHeaderCorrectly()
    {
        $request = m::mock('Illuminate\Http\Request');
        $request->shouldReceive('header')->with('accept')->once()->andReturn('application/vnd.api.v1.0+json');
        $middleware = new ApiMiddleware($this->container, $this->factory, $request);
        $this->assertEquals(['v1.0', 'json'], $middleware->parseAcceptHeader($request));
    }

    public function testDefaultsToFallbackVersionIfNoAcceptHeaderMatch()
    {
        $this->container['config'] = ['api.strict' => false];

        $request = m::mock('Illuminate\Http\Request');
        $request->shouldReceive('header')->with('accept')->once()->andReturn('*\*');

        $middleware = new ApiMiddleware($this->container, $this->factory, $request);
        $this->assertEquals(['v1.0', 'json'], $middleware->parseAcceptHeader($request));
    }

    /**
     * @expectedException j4k\Api\Exceptions\InvalidHeaderException
     */
    public function testsStrictModeThrowsExceptionIfNoAcceptHeaderMatch()
    {
        $this->container['config'] = ['api.strict' => true];

        $request = m::mock('Illuminate\Http\Request');
        $request->shouldReceive('header')->with('accept')->once()->andReturn('*\*');

        $middleware = new ApiMiddleware($this->container, $this->factory, $request);
        $middleware->parseAcceptHeader($request);
    }

}