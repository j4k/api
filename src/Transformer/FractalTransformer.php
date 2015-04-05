<?php

namespace j4k\Api\Transformer;

use Illuminate\Http\Request;
use League\Fractal\Manager as Fractal;
use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Illuminate\Support\Collection as IlluminateCollection;
use Illuminate\Pagination\Paginator as IlluminatePaginator;
use League\Fractal\Resource\Collection as FractalCollection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class FractalTransformer
{
    protected $fractal;

    protected $includeKey;

    protected $includeSeperator;

    protected $eager = true;

    public function __construct(Fractal $fractal, $includeKey = 'include', $includeSeperator = ',', $eager = true)
    {
       $this->fractal = $fractal;
       $this->includeKey = $includeKey;
       $this->includeSeperator = $includeSeperator;
       $this->eager = $eager;
    }

    public function transform($response, $transformer, Request $request, $params = [])
    {
        $resource = $this->createResource($response, $transformer, $params);

        if ($response instanceof IlluminatePaginator) {
            $paginator = $this->createPaginatorAdapter($response);
            $resource->setPaginator($paginator);
        }

        if ($response instanceof EloquentCollection && $this->eager) {
            $eagerLoads = $this->mergeEagerLoads($transformer, $this->fractal->getRequestedIncludes());
            $response->load($eagerLoads);
        }

        return $this->fractal->createData($resource)->toArray();
    }

    protected function createPaginatorAdapter($response)
    {
        // TODO : Create
        // get the paginator and create a new IlluminatePaginatorAdapter
    }

    protected function createResource($response, $transformer, array $parameters)
    {
        $key = isset($parameters['key']) ? $parameters['key'] : null;

        if($response instanceof IlluminatePaginator || $response instanceof IlluminateCollection){
            return new FractalCollection($response, $transformer, $key);
        }

        return new FractalItem($response, $transformer, $key);
    }

    public function parseFractalIncludes(Request $request)
    {
        $includes = $request->get($this->includeKey);

        if(!is_array($includes))
            $includes = array_filter(explode($this->includeSeperator, $includes));

        $this->fractal->parseIncludes($includes);
    }

    protected function mergeEagerLoads($transformer, $requestedIncludes)
    {
        $availableIncludes = array_intersect($transformer->getAvailableIncludes(), (array) $requestedIncludes);

        $includes = array_merge($availableIncludes, $transformer->getDefaultIncludes());

        $eagerLoads = [];

        foreach ($includes as $key => $val) {
            $eagerLoads[] = is_string($key) ? $key : $val;
        }

        return $eagerLoads;
    }

}
