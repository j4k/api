<?php

namespace j4k\Api\Http;

use Closure;
use BadMethodCallException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use j4k\Api\Transformer\TransformerFactory;

class ResponseFactory
{
    /**
     * @var array
     */
    protected $meta = [];
    /**
     * @var TransformerFactory
     */
    protected $transformer;

    /**
     * @param TransformerFactory $transformer
     */
    public function __construct(TransformerFactory $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * @param Collection $collection
     * @param $transformer
     * @param array $parameters
     * @param callable $after
     * @return ResponseBuilder
     */
    public function collection(Collection $collection, $transformer, array $parameters = [], Closure $after = null)
    {
        $collection = $this->transformer->transform($collection, $transformer, $parameters, $after);
        return new ResponseBuilder($collection);
    }

    /**
     * @param $item
     * @param $transformer
     * @param array $parameters
     * @param callable $after
     * @return ResponseBuilder
     */
    public function item($item, $transformer, array $parameters = [], Closure $after = null)
    {
        $item = $this->transformer->transform($item, $transformer, $parameters, $after);
        return new ResponseBuilder($item);
    }

    /**
     * @param LengthAwarePaginator $paginator
     * @param $transformer
     * @param array $parameters
     * @param callable $after
     * @return ResponseBuilder
     */
    public function paginator(LengthAwarePaginator $paginator, $transformer, array $parameters = [], Closure $after = null)
    {
        $paginator = $this->transformer->transform($paginator, $transformer, $parameters, $after);
        return new ResponseBuilder($paginator);
    }

    /**
     * @param null $location
     * @return ResponseBuilder
     */
    public function created($location = null)
    {
        $response = new ResponseBuilder(null);
        $response->setStatusCode(201);

        if(! is_null($location))
            $response->header('Location', $location);

        return $response;
    }

    /**
     * @param $error
     * @param $statusCode
     * @return ResponseBuilder
     */
    public function error($error, $statusCode)
    {
        if(! is_array($error))
            $error = ['message' => $error];

        $error = array_merge(['status_code' => $statusCode], $error);
        $response = new ResponseBuilder($error);
        $response->setStatusCode($statusCode);
        return $response;
    }

    /**
     * @param string $message
     * @return ResponseBuilder
     */
    public function notFound($message = 'The resource could not be found')
    {
        return $this->error($message, 404);
    }

    /**
     * @param string $message
     * @return ResponseBuilder
     */
    public function badRequest($message = 'The request was bad')
    {
        return $this->error($message, 400);
    }

    /**
     * @param string $message
     * @return ResponseBuilder
     */
    public function forbidden($message = 'Forbidden Request')
    {
        return $this->error($message, 403);
    }

    /**
     * @param string $message
     * @return ResponseBuilder
     */
    public function noContent($message = 'No Content')
    {
        return $this->error($message, 204);
    }

    /**
     * @param string $message
     * @return ResponseBuilder
     */
    public function errorConflict($message = 'Entity Conflict')
    {
        return $this->error($message, 409);
    }

    /**
     * @param string $message
     * @return ResponseBuilder
     */
    public function errorInternal($message = 'Internal Server Error')
    {
        return $this->error($message, 500);
    }

    /**
     * @param string $message
     * @return ResponseBuilder
     */
    public function errorUnauthorized($message = 'Unauthorized Request')
    {
        return $this->error($message, 401);
    }

    /**
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (Str::startsWith($method, 'with'))
            return call_user_func_array([$this, Str::camel(substr($method, 4))], $parameters);

        throw new BadMethodCallException('Method '.$method.' does not exist on class.');
    }
}
