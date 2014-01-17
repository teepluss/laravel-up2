## UP2 for Laravel 4

UP2 is a file uploader with polymorphic relations.

### Installation

- [UP2 on Packagist](https://packagist.org/packages/teepluss/up2)
- [UP2 on GitHub](https://github.com/teepluss/laravel4-up2)

To get the lastest version of Theme simply require it in your `composer.json` file.

~~~
"teepluss/up2": "dev-master"
~~~

You'll then need to run `composer install` to download it and have the autoloader updated.

Once Theme is installed you need to register the service provider with the application. Open up `app/config/app.php` and find the `providers` key.

~~~
'providers' => array(

    'Teepluss\Up2\Up2ServiceProvider'

)
~~~

UP2 also ships with a facade which provides the static syntax for creating collections. You can register the facade in the `aliases` key of your `app/config/app.php` file.

~~~
'aliases' => array(

    'UP2' => 'Teepluss\Up2\Facades\Up2'

)
~~~

Publish config using artisan CLI.

~~~
php artisan config:publish teepluss/up2
~~~

Migrate tables.

~~~
php artisan migrate --package=teepluss/up2
~~~

## Usage

First you have to create a morph method for your model that want to use "UP2".

~~~php
class Blog extends Eloquent {

    public function .....

    /**
     * Blog has many files upload.
     *
     * @return Attachment
     */
    public function attachments()
    {
        $model = Config::get('up2::attachments.model');

        return $this->morphToMany('\Teepluss\Up2\Attachments\Eloquent\Attachment', 'attachmentable');
    }

}
~~~

### After create a method "attachments", Blog can use "UP2" to upload files.

Upload file and resizing.

~~~php
// Return an original file meta.
UP2::upload(Blog::find(1), Input::file('userfile'))->getMasterResult();
UP2::upload(User::find(1), Input::file('userfile'))->getMasterResult();

// Return all results files uploaded including resized.
UP2::upload(Product::find(1), Input::file('userfile'))->resize()->getResults();

// If you have other fields in table attachments.
UP2::upload(User::find(1), Input::file('userfile'), array('some_id' => 999))->getMasterResult();
~~~

// UP2 can upload remote file.
UP2::inject(array('remote' => true))->upload(User::find(1), Input::file('userfile'), array('some_id' => 999))->getResults();

Look up a file path.

~~~php
$blogs = Blog::with('attachments')->get();

foreach ($blogs as $blog)
{
    foreach ($blog->attachments as $attachment)
    {
        echo UP2::lookup($attachment->id);

        // or lookup with scale from config.

        echo UP2::lookup($attachment->id)->scale('l');
    }
}
~~~

Remove file(s) from storage.

~~~php
$attachmentId = 'b5540d7e6350589004e02e23feb3dc1f';

// Remove a single file.
UP2::remove($attachmentId);

// Remove all files including resized.
UP2::remove($attachmentId, true);
~~~

## Support or Contact

If you have some problem, Please contact teepluss@gmail.com


[![Support via PayPal](https://rawgithub.com/chris---/Donation-Badges/master/paypal.jpeg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9GEC8J7FAG6JA)
