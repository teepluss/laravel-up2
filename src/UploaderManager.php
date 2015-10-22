<?php 

namespace Teepluss\Up2;

use Closure;
use Illuminate\Support\Manager;
use Teepluss\Up2\StoreInterface;

class UploaderManager extends Manager 
{
    /**
     * Create an instance of the local storage driver.
     *
     * @return \Teepluss\Up2\LocalStorage
     */
    protected function createLocalDriver()
    {
        $config = $this->app['config']['up2.uploader'];

        $config = array_merge($config, $config['drivers']['local']);
        unset($config['drivers']);

        return $this->repository(new LocalStorage($config, $this->app['request'], $this->app['files']));
    }

    /**
     * Create an instance of the S3 storage driver.
     *
     * @return \Teepluss\Up2\S3Storage
     */
    protected function createS3Driver()
    {
        $config = $this->app['config']['up2.uploader'];

        $config = array_merge($config, $config['drivers']['s3']);
        unset($config['drivers']);

        return $this->repository(new S3Storage($config, $this->app['request'], $this->app['files']));
    }

    /**
     * Create a new cache repository with the given implementation.
     *
     * @param  \Teepluss\Up2\StoreInterface  $store
     * @return \Teepluss\Up2\Repository
     */
    protected function repository(StoreInterface $store)
    {
        return new Repository($store);
    }

    /**
     * Get the default uploader driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['up2.uploader.default'];
    }

    /**
     * Set the default uploader driver name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['up2.uploader.default'] = $name;
    }

}