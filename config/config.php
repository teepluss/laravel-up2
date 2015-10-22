<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Attachment Model
    |--------------------------------------------------------------------------
    |
    | When using the "eloquent" driver, we need to know which
    | Eloquent models should be used throughout Up.
    |
    */

    'attachments' => array(

        'model' => '\Teepluss\Up2\Attachments\Eloquent\Attachment'

    ),

    /*
    |--------------------------------------------------------------------------
    | Callback
    |--------------------------------------------------------------------------
    |
    | Placeholder for image not found.
    |
    */

    'placeholder' => function($attachmentId)
    {
        //return URL::asset('placeholder/notfound.png');
        return 'Image not found.';
    },

    /*
    |--------------------------------------------------------------------------
    | Uploader driver
    |--------------------------------------------------------------------------
    |
    | Support local, s3
    |
    */

    'default' => 'local',

    /*
    |--------------------------------------------------------------------------
    | Driver config
    |--------------------------------------------------------------------------
    |
    | Here are each of the storage setup for your application.
    |
    */

    'drivers' => array(

        'local' => array(
            'baseUrl' => \URL::to(''),
            'baseDir' => \App::make('path.public'),
        ),

        's3' => array(
            'key'    => '',
            'secret' => '',
            'region' => 'ap-southeast-1',
            'bucket' => 'teeplus',
        ),

    ),

    /*
    |--------------------------------------------------------------------------
    | Upload type.
    |--------------------------------------------------------------------------
    |
    | Up allow upload type simple, remote, base64
    | by the default set detect.
    |
    */

    'type' => 'detect',

    /*
    |--------------------------------------------------------------------------
    | Append sub directory to 'base_dir'
    |--------------------------------------------------------------------------
    |
    | You can append a sub directories to base path
    | this allow you to use 'Closure'.
    |
    */

    'subpath' => 'uploads',

    /*
    |--------------------------------------------------------------------------
    | All scales to resize.
    |--------------------------------------------------------------------------
    |
    | For image uploaded you can resize to
    | selected or whole of scales.
    |
    */

    'scales' => array(
        //'wm' => array(260, 180),
        //'wl' => array(300, 200),
        //'wx' => array(360, 270),
        //'ww' => array(260, 120),
        'ws' => array(160, 120),
        'l'  => array(200, 200),
        'm'  => array(125, 125),
        's'  => array(64, 64),
        'ss' => array(45, 45)
    ),

    /*
    |--------------------------------------------------------------------------
    | Upload quality.
    |--------------------------------------------------------------------------
    |
    | Quality, resolution and flatten all uploaded.
    |
    */

    'quality' => array(
        'jpeg' => 90,
        'png'  => 90
    ),

    /*
    |--------------------------------------------------------------------------
    | Callback on each file uploaded.
    |--------------------------------------------------------------------------
    |
    | This should be closure to listen when each file uploaded.
    |
    */

    'onUpload' => null,

    /*
    |--------------------------------------------------------------------------
    | Callback on all files uploaded.
    |--------------------------------------------------------------------------
    |
    | This should be closure to listen when all files uploaded.
    |
    */

    'onComplete' => null,

    /*
    |--------------------------------------------------------------------------
    | Callback on all files deleted.
    |--------------------------------------------------------------------------
    |
    | This should be closure to listen when file deleted.
    |
    */

    'onRemove' => null,

);
