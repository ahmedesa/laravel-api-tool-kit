<?php

namespace Essa\APIToolKit\Tests;

use Essa\APIToolKit\MediaHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaHelperTest extends TestCase
{
    private string $testingImage = __DIR__ . '/Images/laravel-api-tool-kit.png';

    /** @test */
    public function itUploadsFile()
    {
        Storage::fake();

        $file = $this->getUploadedFile();

        $path = 'uploads/images';

        $uploadedPath = MediaHelper::uploadFile($file, $path);

        Storage::assertExists($uploadedPath);
    }

    /** @test */
    public function itUploadsFileWithOriginalName()
    {
        Storage::fake();

        $file = $this->getUploadedFile();

        $path = 'uploads/images';

        $uploadedPath = MediaHelper::uploadFile($file, $path, null, true);

        Storage::assertExists($uploadedPath);
        Storage::assertExists($path . '/test1.jpg');
    }

    /** @test */
    public function itUploadsMultipleFiles()
    {
        Storage::fake();

        $files = [
            $this->getUploadedFile('test1.jpg'),
            $this->getUploadedFile('test2.jpg'),
        ];

        $path = 'uploads/images';

        $uploadedPaths = MediaHelper::uploadMultiple($files, $path);

        foreach ($uploadedPaths as $uploadedPath) {
            Storage::assertExists($uploadedPath);
        }
    }

    /** @test */
    public function itUploadsBase64Image()
    {
        Storage::fake();

        $base64Image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/w8AAwAB/AL+f4R4AAAAASUVORK5CYII=';

        $path = 'uploads/images';

        $uploadedPath = MediaHelper::uploadBase64Image($base64Image, $path);

        Storage::assertExists($uploadedPath);
    }

    /** @test */
    public function itDeletesFile()
    {
        Storage::fake();

        $file = $this->getUploadedFile();

        $path = 'uploads/images';

        $uploadedPath = MediaHelper::uploadFile($file, $path);

        MediaHelper::deleteFile($uploadedPath);

        Storage::assertMissing($uploadedPath);
    }

    /** @test */
    public function itGetsFileFullPath()
    {
        Storage::fake();

        $file = $this->getUploadedFile();

        $path = 'uploads/images';

        $uploadedPath = MediaHelper::uploadFile($file, $path);

        $fullPath = MediaHelper::getFileFullPath($uploadedPath);

        $this->assertEquals(Storage::url($uploadedPath), $fullPath);
    }

    private function getUploadedFile(string $name = 'test1.jpg'): UploadedFile
    {
        return new UploadedFile(
            $this->testingImage,
            $name,
            'image/jpeg',
            null,
            true
        );
    }
}
