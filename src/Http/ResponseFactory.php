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
    protected $meta = [];

    protected $transformer;

    public function __construct(TransformerFactory $transformer)
    {
        $this->transformer = $transformer;
    }

    public function collection(Collection $collection, $transformer, array $parameters = [], Closure $after = null)
    {
        $collection = $this->transformer->transform($collection, $transformer, $parameters, $after);
        return new ResponseBuilder($collection);
    }

    public function item($item, $transformer, array $parameters = [], Closure $after = null)
    {
        $item = $this->transformer->transform($item, $transformer, $parameters, $after);
        return new ResponseBuilder($item);
    }

    public function paginator(LengthAwarePaginator $paginator, $transformer, array $parameters = [], Closure $after = null)
    {
        $paginator = $this->transformer->transform($paginator, $transformer, $parameters, $after);
        return new ResponseBuilder($paginator);
    }

    public function created($location = null)
    {
        $response = new ResponseBuilder(null);
        $response->setStatusCode(201);

        if(! is_null($location))
            $response->header('Location', $location);

        return $response;
    }

    public function error($error, $statusCode)
    {
        if(! is_array($error))
            $error = ['message' => $error];

        $error = array_merge(['status_code', $statusCode], $error);

        $response = new ResponseBuilder($error);
        $response->setStatusCode($statusCode);
        return $response;
    }

    public function notFound($message = 'The resource could not be found.')
    {
        return $this->error($message, 404);
    }

    public function badRequest($message = 'The request was bad.')
    {
        return $this->error($message, 400);
    }

    public function forbidden($message = 'Forbidden Request.')
    {
        return $this->error($message, 403);
    }

    public function errorInternal($message = 'Internal Server Error')
    {
        return $this->error($message, 500);
    }

    public function errorUnauthorized($message = 'Unauthorized Request')
    {
        return $this->error($message, 401);
    }

    public function __call($method, $parameters)
    {
        if (Str::startsWith($method, 'with'))
            return call_user_func_array([$this, Str::camel(substr($method, 4))], $parameters);

        throw new BadMethodCallException('Method '.$method.' does not exist on class.');
    }
}
