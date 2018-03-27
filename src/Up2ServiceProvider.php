<?php 

namespace Teepluss\Up2;

use Imagine\Image\Box;
use Imagine\Image\Point;
use Illuminate\Support\ServiceProvider;
use Teepluss\Up2\Attachments\Eloquent\Provider as AttachmentProvider;

class Up2ServiceProvider extends ServiceProvider 
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register package.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('up2.php')
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'migrations');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__.'/../config/config.php';

        // Merge config from vendor, override by user config.
        $this->mergeConfigFrom($configPath, 'up2');

        $this->registerAttachmentProvider();
        $this->registerUploader();
        $this->registerUp2();
    }

    /**
     * Register attachment provider.
     *
     * @return void
     */
    protected function registerAttachmentProvider()
    {
        /*
        $this->app['up2.attachment'] = $this->app->share(function($app) {
            $model = $app['config']->get('up2.config.attachments.model');
            return new AttachmentProvider($model);
        });
        */
        
        $this->app->singleton('up2.attachment', function($app) {
            $model = $app['config']->get('up2.config.attachments.model');
            return new AttachmentProvider($model);
        });
    }

    /**
     * Register uploader adapter.
     *
     * @return void
     */
    public function registerUploader()
    {
        /*
        $this->app['up2.uploader'] = $this->app->share(function($app) {
            return new UploaderManager($app);
        });
        */
        
        $this->app->singleton('up2.uploader', function($app) {
            return new UploaderManager($model);
        });
    }

    /**
     * Register core class.
     *
     * @return void
     */
    protected function registerUp2()
    {
        /*
        $this->app['up2'] = $this->app->share(function($app) {
            $app['up2.loaded'] = true;
            return new Up2($app['config'], $app['up2.attachment'], $app['up2.uploader']);
        });
        */
        
        $this->app->singleton('up2', function($app) {
            $app['up2.loaded'] = true;
            return new Up2($app['config'], $app['up2.attachment'], $app['up2.uploader']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('attach', 'up2');
    }

}
