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
        $this->assertEquals($expectedWidth, $width, 'The width of resized image is not equal to expected');
    }

    public function test_if_image_is_resized_with_no_params_and_returns_correct_content_type()
    {
        $image = $this->getUploadedImage();
        $response = $this->call('POST', '/api/optimize', [], [], ['picture' => $image]);
        $file = storage_path('app/resized/') . 'example_w640.jpg';
        $this->assertContains('image/', $response->headers->get('Content-Type'));
    }

    public function test_if_corrupted_image_returns_json_and_http_code_204()
    {
        $image = $this->getUploadedCorruptImage();
        $response = $this->call('POST', '/api/optimize', [], [], ['picture' => $image]);
        echo $response->getContent();
        //$this->assertContains('json', $response->headers->get('Content-Type'));
        $this->assertEquals(204, $response->getStatusCode());
    }

    public function test_if_image_height_is_100()
    {
        $image = $this->getUploadedImage();
        $expectedHeight = 100;
        $this->call('POST', '/api/optimize', ['height' => 100], [], ['picture' => $image]);
        $file = storage_path('app/resized/') . 'example_w640h100.jpg';
        list($width, $height) = getimagesize($file);
        $this->assertEquals($expectedHeight, $height, 'The height of resized image is not equal to expected');
    }

    public function test_if_small_image_is_not_upsized()
    {
        $image = $this->getSmallUploadedImage();
        $expectedWidth = 300;
        $this->call('POST', '/api/optimize', ['width' => 600], [], ['picture' => $image]);
        $file = storage_path('app/resized/') . 'example-small_w600.jpg';
        list($width) = getimagesize($file);
        $this->assertEquals($expectedWidth, $width,
            'The size of resized image is not equal to expected because was upsized');
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


    public function test_resize_remote_image_width_when_is_400_is_not_upsized()
    {
        $image = $this->getDownloadedImage();
        $expectedWidth = 400;
        $resizeTo = 600;
        $response = $this->call('GET',
            '/optimize?image=' . 'https://dummyimage.com/400x400/000/fff.jpg' . '&width=' . $resizeTo);
        Storage::put('compressed/test_' . basename($image), $response->getContent());
        list($width, $height) = getimagesize(storage_path('app/compressed/') . 'test_' . basename($image));
        unlink(storage_path('app/compressed/') . 'test_' . basename($image));
        $this->assertResponseOk();
        $this->assertEquals($expectedWidth, $width,
            'The size of resized image is not equal to expected because was upsized');
    }


}
