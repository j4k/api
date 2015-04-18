<?php

namespace j4k\Api\Transformer;

use Closure;

interface TransformerContract
{

    /**
     * @param $response
     * @param $transformer
     * @param array $params
     * @param callable $after
     * @return mixed
     */
    public function transform($response, $transformer, $params = [], Closure $after = null);

}
