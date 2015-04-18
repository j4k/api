<?php

namespace j4k\Api\Test\Http;

use Mockery as m;
use Illuminate\Support\Collection;
use j4k\Api\Http\ResponseFactory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ResponseFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->transformer = m::mock('j4k\Api\Transformer\TransformerFactory');
        $this->factory = new ResponseFactory($this->transformer);
    }

    public function tearDown()
    {
        m::close();
    }

    public function testMakingACreatedResponse()
    {
        $response = $this->factory->created()->build();
        $responseWithLocation = $this->factory->created('testLocation')->build();

        $this->assertEquals($response->getStatusCode(), 201);
        $this->assertFalse($response->headers->has('Location'));

        $this->assertEquals($responseWithLocation->getStatusCode(), 201);
        $this->assertTrue($responseWithLocation->headers->has('Location'));
        $this->assertEquals($responseWithLocation->headers->get('Location'), 'testLocation');
    }

    public function testCreateCollectionRegistersUnderlyingClassWithTransformer()
    {
        $this->transformer->shouldReceive('transform')->twice();

        $this->factory->collection(new Collection([new \stdClass]), $this->transformer);
        $this->factory->withCollection(new Collection([new \stdClass]), $this->transformer);
    }

    public function testCreateItemRegistersUnderlyingClassWithTransformer()
    {
        $this->transformer->shouldReceive('transform')->twice();

        $this->factory->item(new \stdClass, $this->transformer);
        $this->factory->withItem(new \stdClass, $this->transformer);
    }

    public function testPaginatorRegistersUnderlyingClassWithTransformer()
    {
        $paginator = m::mock('Illuminate\Contracts\Pagination\LengthAwarePaginator');

        $this->transformer->shouldReceive('transform')->twice();

        $this->factory->paginator($paginator, $this->transformer);
        $this->factory->withPaginator($paginator, $this->transformer);
    }

    public function testCreatingErrorResponse()
    {
        $errorResponse = $this->factory->error('testError', 500)->build();
        $this->assertEquals(500, $errorResponse->getStatusCode());
        $this->assertNotEquals(200, $errorResponse->getStatusCode());

        $this->assertEquals('{"status_code":500,"message":"testError"}', $errorResponse->getContent());
    }



}
