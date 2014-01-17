<?php namespace Teepluss\Up2;

use Illuminate\Support\ServiceProvider;
use Teepluss\Up2\Attachments\Eloquent\Provider as AttachmentProvider;

class Up2ServiceProvider extends ServiceProvider {

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
		$this->package('teepluss/up2');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
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
		$this->app['up2.attachment'] = $this->app->share(function($app)
		{
			$model = $app['config']->get('up2::attachments.model');

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
		$this->app['up2.uploader'] = $this->app->share(function($app)
		{
			return new Uploader($app['config'], $app['request'], $app['files']);
		});
	}

	/**
	 * Register core class.
	 *
	 * @return void
	 */
	protected function registerUp2()
	{
		$this->app['up2'] = $this->app->share(function($app)
		{
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