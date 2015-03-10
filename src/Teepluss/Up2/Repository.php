<?php namespace Teepluss\Up2;

use Closure;
use Carbon\Carbon;
use Teepluss\Up2\StoreInterface;
use Illuminate\Support\Traits\MacroableTrait;

class Repository {

    use MacroableTrait {
        __call as macroCall;
    }

    /**
     * The cache store implementation.
     *
     * @var \Illuminate\Cache\StoreInterface
     */
    protected $store;

    /**
     * Create a new cache repository instance.
     *
     * @param  \Illuminate\Cache\StoreInterface  $store
     */
    public function __construct(StoreInterface $store)
    {
        $this->store = $store;
    }

    /**
     * Get the cache store implementation.
     *
     * @return \Illuminate\Cache\StoreInterface
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * Handle dynamic calls into macros or pass missing methods to the store.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method))
        {
            return $this->macroCall($method, $parameters);
        }

        return call_user_func_array(array($this->store, $method), $parameters);
    }

}