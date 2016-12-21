# Image Optimizer and Resizer API powered by Laravel
### Google Pagespeed friendly

[![Build Status](https://scrutinizer-ci.com/g/arzola/image-optimizer/badges/build.png?b=master)](https://scrutinizer-ci.com/g/arzola/image-optimizer/build-status/master)

This laravel app provide a sweet API to optimize and resize images with a simple http call.

This package use the awesome wrapper [Laravel Imageoptimizer](https://github.com/approached/laravel-image-optimizer) to handle image transformations.

Key features

- API interface to optimize images on your application with a single POST request
- You can host on your server
- Laravel Queue Jobs (TODO)
- Battle Tested

##Usage

#### API POST request using Guzzle

Optimize and resize image to 200px width and auto height

> Call */api/optimize*
> params: 
> - picture: multipart file object
> - width: the maximum desired width in pixels
> - height: the maximum desired height in pixels (auto if empty)

```php
    $file = open(base_path('your/image/path'), 'r');
    $response = $client->request('POST', 'http://imageoptim.dev/api/optimize', [
        'multipart' => [
            [
                'name'     => 'width',
                'contents' => 200
            ],
            [
                'name'     => 'picture',
                'contents' => $file
            ]
        ]
    ]);
```
Now you can save or do whatever you want with your resized and optimized image.

```php
    $yourResizedImageData = $response->getContent();
```
