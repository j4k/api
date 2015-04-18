<?php

namespace j4k\Api\Test\Stubs;

class TransformerStub
{
    public function transform(ModelStub $model)
    {
        return [
            'key' => 'val'
        ];
    }
}
