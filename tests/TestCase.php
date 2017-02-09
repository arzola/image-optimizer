<?php

abstract class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://imageoptim.dev';
    protected $tempUploadedFile;
    protected $tempUploadedSmallFile;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    public function setUp()
    {
        parent::setUp();
        $example = __DIR__ . '/fakedata/example.jpg';
        $tempFile = sys_get_temp_dir() . '/example.jpg';
        copy($example, $tempFile);
        $this->tempUploadedFile = $tempFile;
        $exampleSmall = __DIR__ . '/fakedata/example-small.jpg';
        $tempFileSmall = sys_get_temp_dir() . '/example-small.jpg';
        copy($exampleSmall, $tempFileSmall);
        $this->tempUploadedSmallFile = $tempFileSmall;
    }

    public function tearDown()
    {
        unlink($this->tempUploadedFile);
        unlink($this->tempUploadedSmallFile);
        collect(Storage::allFiles('compressed'))->each(function ($file) {
            unlink(storage_path('app/') . $file);
        });
        collect(Storage::allFiles('remote'))->each(function ($file) {
            unlink(storage_path('app/') . $file);
        });
        collect(Storage::allFiles('resized'))->each(function ($file) {
            unlink(storage_path('app/') . $file);
        });
        parent::tearDown();
    }

    public function getUploadedImage()
    {
        return new \Illuminate\Http\UploadedFile($this->tempUploadedFile, 'example.jpg',
            filesize($this->tempUploadedFile), 'image/jpeg', null, true);
    }

    public function getSmallUploadedImage()
    {
        return new \Illuminate\Http\UploadedFile($this->tempUploadedSmallFile, 'example-small.jpg',
            filesize($this->tempUploadedSmallFile), 'image/jpeg', null, true);
    }

    public function getDownloadedImage()
    {
        return 'http://www.cdmx.gob.mx/storage/app/uploads/public/584/599/785/58459978551a0763763395.jpg';
    }

}
