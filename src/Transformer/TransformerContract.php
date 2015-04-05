<?php

namespace j4k\Api\Transformer;

use Closure;

interface TransformerContract
{

    public function transform($response, $transformer, $params = [], Closure $after = null);

}
