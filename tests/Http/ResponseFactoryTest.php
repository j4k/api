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
        $errorResponse = $this->factory->error('This is a test error', 500)->build();
        $this->assertEquals(500, $errorResponse->getStatusCode());
        $this->assertNotEquals(200, $errorResponse->getStatusCode());

        $this->assertEquals('{"status_code":500,"message":"This is a test error"}', $errorResponse->getContent());
    }

    public function testNotFoundResponse()
    {
        $notFoundResponse = $this->factory->notFound()->build();
        $notFoundResponseWithMessage = $this->factory->notFound('The specified resource was not found.')->build();

        $this->assertEquals(404, $notFoundResponse->getStatusCode());
        $this->assertEquals(404, $notFoundResponseWithMessage->getStatusCode());

        $this->assertEquals('{"status_code":404,"message":"The resource could not be found"}', $notFoundResponse->getContent());
        $this->assertEquals('{"status_code":404,"message":"The specified resource was not found."}', $notFoundResponseWithMessage->getContent());
    }

    public function testBadRequestResponse()
    {
        $badRequestResponse = $this->factory->badRequest()->build();
        $badRequestResponseWithMessage = $this->factory->badRequest('There was an internal server error.')->build();

        $this->assertEquals(400, $badRequestResponse->getStatusCode());
        $this->assertEquals(400, $badRequestResponseWithMessage->getStatusCode());

        $this->assertEquals('{"status_code":400,"message":"The request was bad"}', $badRequestResponse->getContent());
        $this->assertEquals('{"status_code":400,"message":"There was an internal server error."}', $badRequestResponseWithMessage->getContent());
    }

    public function testForbiddenResponse()
    {
        $forbiddenResponse = $this->factory->forbidden()->build();
        $forbiddenResponseWithMessage = $this->factory->forbidden('The request was forbidden.')->build();

        $this->assertEquals(403, $forbiddenResponse->getStatusCode());
        $this->assertEquals(403, $forbiddenResponseWithMessage->getStatusCode());

        $this->assertEquals('{"status_code":403,"message":"Forbidden Request"}', $forbiddenResponse->getContent());
        $this->assertEquals('{"status_code":403,"message":"The request was forbidden."}', $forbiddenResponseWithMessage->getContent());
    }

    public function testNoContentResponse()
    {
        $noContentResponse = $this->factory->noContent()->build();
        $noContentResponseWithMessage = $this->factory->noContent('The resource was successfully updated.')->build();

        $this->assertEquals(204, $noContentResponse->getStatusCode());
        $this->assertEquals(204, $noContentResponseWithMessage->getStatusCode());

        $this->assertEquals('{"status_code":204,"message":"No Content"}', $noContentResponse->getContent());
        $this->assertEquals('{"status_code":204,"message":"The resource was successfully updated."}', $noContentResponseWithMessage->getContent());
    }

    public function testErrorConflict()
    {
        $errorConflictResponse = $this->factory->errorConflict()->build();
        $errorConflictResponseWithMessage = $this->factory->errorConflict('There was a conflict updating the resource.')->build();

        $this->assertEquals(409, $errorConflictResponse->getStatusCode());
        $this->assertEquals(409, $errorConflictResponseWithMessage->getStatusCode());

        $this->assertEquals('{"status_code":409,"message":"Entity Conflict"}', $errorConflictResponse->getContent());
        $this->assertEquals('{"status_code":409,"message":"There was a conflict updating the resource."}', $errorConflictResponseWithMessage->getContent());
    }

    public function testErrorInternalConflict()
    {
        $errorInternalResponse = $this->factory->errorInternal()->build();
        $errorInternalResponseWithMessage = $this->factory->errorInternal('You blew the server up.')->build();

        $this->assertEquals(500, $errorInternalResponse->getStatusCode());
        $this->assertEquals(500, $errorInternalResponseWithMessage->getStatusCode());

        $this->assertEquals('{"status_code":500,"message":"Internal Server Error"}', $errorInternalResponse->getContent());
        $this->assertEquals('{"status_code":500,"message":"You blew the server up."}', $errorInternalResponseWithMessage->getContent());
    }

    public function testErrorUnauthorized()
    {
        $errorUnauthorizedResponse = $this->factory->errorUnauthorized()->build();
        $errorUnauthorizedResponseWithMessage = $this->factory->errorUnauthorized('You are not permitted to access this resource.')->build();

        $this->assertEquals(401, $errorUnauthorizedResponse->getStatusCode());
        $this->assertEquals(401, $errorUnauthorizedResponseWithMessage->getStatusCode());

        $this->assertEquals('{"status_code":401,"message":"Unauthorized Request"}', $errorUnauthorizedResponse->getContent());
        $this->assertEquals('{"status_code":401,"message":"You are not permitted to access this resource."}', $errorUnauthorizedResponseWithMessage->getContent());
    }
}
