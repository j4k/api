<?php

namespace j4k\Api\Middleware;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Cache\CacheManager;
use Illuminate\Support\Collection;
use j4k\Api\Middleware\RateLimit\Throttle;

class RateLimit
{
    public $config = [
        'limit' => 0,
        'expires' => 0
    ];

    protected $container;

    protected $cache;

    protected $throttle;

    protected $cacheKey;

    public function __construct(Container $container, CacheManager $cache )
    {
        $this->cache = $cache;
        $this->container = $container;
    }

    /**
     * Handle a request
     *
     * @param string $phrase Phrase to return
     *
     * @return string Returns the phrase passed in
     */
    public function handle($request, Closure $next)
    {
        if( null !== $request->route()->getAction()['throttle']){
            $this->config = array_merge($this->config, $request->route()->getAction()['throttle']);
        }

        if( $this->limit > 0  || $this->expires > 0){
            $this->throttle = new Throttle(['limit' => $this->limit, 'expires' => $this->expires]);
            $this->cacheKey = md5($request->path());
        }

        // if this isn't a throttled route then return
        if (is_null($this->throttle)) return $next( $request );

        $this->prepareCache();

        $this->cache('requests', 0, $this->throttle->getExpires());
        $this->cache('expires', $this->throttle->getExpires(), $this->throttle->getExpires());
        $this->cache('reset', time() + ($this->throttle->getExpires() * 60), $this->throttle->getExpires());
        $this->increment('requests');

        return $next( $request );
    }

    protected function prepareCache()
    {
        if ($this->retrieve('expires') != $this->throttle->getExpires()) {
            $this->forget('requests');
            $this->forget('expires');
            $this->forget('reset');
        }
    }

    protected function key($key)
    {
        return sprintf('api.%s.%s.%s', $this->keyPrefix, $key, $this->getRateLimiter());
    }

    public function getRateLimiter()
    {
        return call_user_func($this->limiter, $this->container, $this->request);
    }

    public function setRateLimiter(callable $limiter)
    {
        $this->limiter = $limiter;
    }

    public function requestWasRateLimited()
    {
        return ! is_null($this->throttle);
    }

    protected function cache($key, $value, $minutes)
    {
        $this->cache->add($this->key($key), $value, $minutes);
    }

    protected function retrieve($key)
    {
        return $this->cache->get($this->key($key));
    }

    protected function increment($key)
    {
        $this->cache->increment($this->key($key));
    }

    protected function forget($key)
    {
        $this->cache->forget($this->key($key));
    }

    public function __get($val)
    {
        if(array_key_exists($val, $this->config)){
            return $this->config[$val];
        }
        return null;
    }

    public function __set($key, $val)
    {
        $this->data[$key] = $val;
    }
}
