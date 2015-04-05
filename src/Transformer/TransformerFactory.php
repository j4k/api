<?php

namespace j4k\Api\Transformer;

use Closure;
use RuntimeException;
use Illuminate\Support\Collection;
use Illuminate\Container\Container;
use Illuminate\Pagination\Paginator;

class TransformerFactory
{

    /**
     * Container Instance
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * Transformation Class
     */
    protected $transformer;

    /**
     * Construct
     */
    public function __construct(Container $container, TransformerContract $transformer)
    {
        $this->container = $container;
        $this->transformer = $transformer;
    }

    public function transform($response, $transformer, array $parameters = [], Closure $after = null)
    {
        return $this->transformer->transform($response, $transformer, $parameters, $after);
    }

}
