<?php

use Teepluss\Up2\Facades\Up2;

if (! function_exists('up')) {
    /**
     * New instance of UP2.
     *
     * @return \Teepluss\Up2\Facades\Up2
     */
    function up()
    {
        return app('up2');
    }
}
