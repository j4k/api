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

    protected function key($str)
    {
        return $this->cacheKey;
    }

    /**
     * Determine if the request was rate limited.
     *
     * @return bool
     */
    public function requestWasRateLimited()
    {
        return ! is_null($this->throttle);
    }

    /**
     * Cache a value under a given key for a certain amount of minutes.
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $minutes
     *
     * @return void
     */
    protected function cache($key, $value, $minutes)
    {
        $this->cache->add($this->key($key), $value, $minutes);
    }
    /**
     * Retrieve a value from the cache store.
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function retrieve($key)
    {
        return $this->cache->get($this->key($key));
    }
    /**
     * Increment a key in the cache.
     *
     * @param string $key
     *
     * @return void
     */
    protected function increment($key)
    {
        $this->cache->increment($this->key($key));
    }
    /**
     * Forget a key in the cache.
     *
     * @param string $key
     *
     * @return void
     */
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
