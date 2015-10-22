<?php 

namespace Teepluss\Up2;

use Closure;
use Carbon\Carbon;
use Teepluss\Up2\StoreInterface;
use Illuminate\Support\Traits\Macroable;

class Repository 
{
    use Macroable {
        __call as macroCall;
    }

    /**
     * The uploader store implementation.
     *
     * @var \Teepluss\Up2\StoreInterface
     */
    protected $store;

    /**
     * Create a new uploader repository instance.
     *
     * @param  \Teepluss\Up2\StoreInterface  $store
     */
    public function __construct(StoreInterface $store) 
    {
        $this->store = $store;
    }

    /**
     * Get the uploader store implementation.
     *
     * @return \Teepluss\Up2\StoreInterface
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
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return call_user_func_array([$this->store, $method], $parameters);
    }

}