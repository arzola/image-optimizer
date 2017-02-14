<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Options for image transforming
     |--------------------------------------------------------------------------
     |
     | Bin path you can check easy with follow command in a shell:
     | which optipng
     |
     */
    'options' => [
        'ignore_errors' => false,

        'pngquant_bin'     => '/usr/local/bin/pngquant',
        'pngquant_options' => ['--force'],

        'gifsicle_bin'     => '/usr/local/bin/gifsicle',
        'gifsicle_options' => ['-b', '-O5'],

        'jpegoptim_bin'     => '/usr/local/bin/jpegoptim',
        'jpegoptim_options' => ['--strip-all','--size=500'],

    ],


    /*
     |--------------------------------------------------------------------------
     | Transformer for image
     |--------------------------------------------------------------------------
     |
     | You can choice which tranformer you will use
     |
     */
    'transform_handler' => [
        'png'  => 'pngquant',
        'jpg'  => 'jpegoptim',
        'jpeg' => 'jpegoptim',
        'gif'  => 'gifsicle',
    ],

    /*
     |--------------------------------------------------------------------------
     | Log file
     |--------------------------------------------------------------------------
     |
     | Only for image optimize errors
     |
     */
    'log_file' => storage_path().'/logs/image_optimize.log',

];
