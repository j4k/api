<?php

namespace j4k\Api\Middleware\RateLimit;


class Throttle {

    protected $options = ['limit' => 60, 'expires' => 60];

    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getLimit()
    {
        return $this->options['limit'];
    }

    public function getExpires()
    {
        return $this->options['expires'];
    }

}