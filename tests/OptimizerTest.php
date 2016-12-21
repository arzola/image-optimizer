<?php

class OptimizerTest extends TestCase
{
    public function test_if_image_is_uploaded()
    {
        $image = $this->getUploadedImage();
        $this->assertEquals('jpg', $image->getClientOriginalExtension());
        $this->assertGreaterThanOrEqual(568993, $image->getSize(), 'THe file size is not enough');
    }

    public function test_if_image_is_saved()
    {
        $image = $this->getUploadedImage();
        $this->call('POST', '/api/optimize', [], [], ['picture' => $image]);
        $file = storage_path('app/resized/') . 'example_w640.jpg';
        $this->assertResponseOk();
        $this->assertFileExists($file);
    }

    public function test_if_image_is_resized_to_500()
    {
        $image = $this->getUploadedImage();
        $expectedWidth = 500;
        $this->call('POST', '/api/optimize', ['width' => 500], [], ['picture' => $image]);
        $file = storage_path('app/resized/') . 'example_w500.jpg';
        list($width) = getimagesize($file);
        $this->assertEquals($expectedWidth, $width, 'The size of resized image is not equal to expected');
    }

    public function test_if_image_is_resized_to_1000()
    {
        $image = $this->getUploadedImage();
        $expectedWidth = 1000;
        $this->call('POST', '/api/optimize', ['width' => 1000], [], ['picture' => $image]);
        $file = storage_path('app/resized/') . 'example_w1000.jpg';
        list($width) = getimagesize($file);
        $this->assertEquals($expectedWidth, $width, 'The size of resized image is not equal to expected');
    }

    public function test_if_image_is_resized_with_no_params()
    {
        $image = $this->getUploadedImage();
        $expectedWidth = 640;
        $this->call('POST', '/api/optimize', [], [], ['picture' => $image]);
        $file = storage_path('app/resized/') . 'example_w640.jpg';
        list($width) = getimagesize($file);
        $this->assertEquals($expectedWidth, $width, 'The size of resized image is not equal to expected');
    }

    public function test_if_image_height_is_100()
    {
        $image = $this->getUploadedImage();
        $expectedHeight = 100;
        $this->call('POST', '/api/optimize', ['height' => 100], [], ['picture' => $image]);
        $file = storage_path('app/resized/') . 'example_w640h100.jpg';
        list($width, $height) = getimagesize($file);
        $this->assertEquals($expectedHeight, $height, 'The size of resized image is not equal to expected');
    }

    public function test_resize_remote_image_to_default()
    {
        $image = $this->getDownloadedImage();
        $expectedWidth = 640;
        $response = $this->call('GET', '/optimize?image=' . $this->getDownloadedImage());
        $this->assertResponseOk();
    }

    public function test_resize_remote_image_width_200()
    {
        $image = $this->getDownloadedImage();
        $expectedWidth = 200;
        $response = $this->call('GET', '/optimize?image=' . $this->getDownloadedImage() . '&width=' . $expectedWidth);
        Storage::put('compressed/test_' . basename($image), $response->getContent());
        list($width, $height) = getimagesize(storage_path('app/compressed/') . 'test_' . basename($image));
        unlink(storage_path('app/compressed/') . 'test_' . basename($image));
        $this->assertResponseOk();
        $this->assertEquals($expectedWidth, $width, 'The size of resized image is not equal to expected');
    }


}
