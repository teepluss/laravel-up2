<?php 

namespace Teepluss\Up2;

interface StoreInterface {

    /**
     * Open the location path.
     *
     * $name don't need to include path.
     *
     * @param   string  $name
     * @return  Attach
     */
    public function open($name);

    /**
     * Generate a view link.
     *
     * @param   string  $path
     * @return  string
     */
    public function url($path);

    /**
     * Uplaod a file to destination.
     *
     * @return Attach
     */
    public function upload();

    /**
     * Generate file result format.
     *
     * @param   string  $location
     * @param   string  $scale
     * @return  array
     */
    //public function results($location, $scale = null);

    /**
     * Resize master image file.
     *
     * @param   array   $sizes
     * @return  Attach
     */
    public function resize($sizes = null);

    /**
     * Remove master image file.
     *
     * @return  Attach
     */
    public function remove();

}