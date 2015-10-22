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
    }

);
