<?php

namespace j4k\Api\Transformer;

use Closure;
use League\Fractal\Manager as Fractal;
use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Illuminate\Support\Collection as IlluminateCollection;
use League\Fractal\Resource\Collection as FractalCollection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as IlluminatePaginator;
use League\Fractal\Serializer\JsonApiSerializer as FractalJsonApiSerializer;

class FractalTransformer implements TransformerContract
{
    /**
     * @var Fractal
     */
    protected $fractal;

    /**
     * @var string
     */
    protected $includeKey;

    /**
     * @var string
     */
    protected $includeSeperator;

    /**
     * @var bool
     */
    protected $eager = true;

    /**
     * @param Fractal $fractal
     * @param string $includeKey
     * @param string $includeSeperator
     * @param bool $eager
     */
    public function __construct(Fractal $fractal, $includeKey = 'include', $includeSeperator = ',', $eager = true)
    {
       $this->fractal = $fractal;
       $this->fractal->setSerializer(new FractalJsonApiSerializer());
       $this->includeKey = $includeKey;
       $this->includeSeperator = $includeSeperator;
       $this->eager = $eager;

    }

    /**
     * @param $response
     * @param $transformer
     * @param array $params
     * @param Closure $after
     * @return array
     */
    public function transform($response, $transformer, $params = [], Closure $after = null)
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

    /**
     * @param IlluminatePaginator $paginator
     * @return IlluminatePaginatorAdapter
     */
    protected function createPaginatorAdapter(IlluminatePaginator $paginator)
    {
        return new IlluminatePaginatorAdapter($paginator);
    }

    /**
     * @param $response
     * @param $transformer
     * @param array $parameters
     * @return FractalCollection|FractalItem
     */
    protected function createResource($response, $transformer, array $parameters)
    {
        $key = isset($parameters['key']) ? $parameters['key'] : null;

        if($response instanceof IlluminatePaginator || $response instanceof IlluminateCollection){
            return new FractalCollection($response, $transformer, $key);
        }

        return new FractalItem($response, $transformer, $key);
    }

    /**
     * @param $transformer
     * @param $requestedIncludes
     * @return array
     */
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
