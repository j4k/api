<?php

namespace j4k\Api\Test\Http;

use Mockery as m;
use Illuminate\Cookie\CookieJar as Cookie;
use j4k\Api\Http\ResponseBuilder;

class ResponseBuilderTest extends \PHPUnit_Framework_TestCase
{

    public function tearDown()
    {
        m::close();
    }

    public function testAddingMeta()
    {
        $builder = new ResponseBuilder([]);
        $builder->meta('foo', 'bar');

        $response = $builder->build();

        $this->assertEquals('{"meta":{"foo":"bar"}}', $response->getContent());
    }

    public function testBuildingWithCustomStatusCodeAndHeaders()
    {
        $builder = new ResponseBuilder(['key' => 'value']);

        $builder->setStatusCode(404);
        $builder->withHeader('Header', 'value');

        $response = $builder->build();

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('value', $response->headers->get('Header'));
    }

}