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
     * Transformer Bindings
     * @var Array
     */
    protected $bindings;

    /**
     * Transformation Class
     */
    protected $transformer;

    /**
     * Construct
     */
    public function __construct(Container $container, TransformerInterface $transformer)
    {
        $this->container = $container;
        $this->transformer = $transformer;
    }

    /**
     * Register a transformer
     */

}
