<?php 

namespace Teepluss\Up2\Facades;

use Illuminate\Support\Facades\Facade;

class Up2 extends Facade 
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'up2'; }

}