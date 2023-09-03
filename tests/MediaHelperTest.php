<?php

namespace Essa\APIToolKit\Tests;

use Essa\APIToolKit\MediaHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaHelperTest extends TestCase
{
    public const BASE_64_IMAGE = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/w8AAwAB/AL+f4R4AAAAASUVORK5CYII=';

    private string $testingImage = __DIR__ . '/Images/laravel-api-tool-kit.png';

    /** @test */
    public function itUploadsFile(): void
    {
        Storage::fake();

        $file = $this->getUploadedFile();

        $path = 'uploads/images';

        $disk = 'public';

        $uploadedPath = MediaHelper::uploadFile(file: $file, path: $path, disk: $disk);

        Storage::disk($disk)->assertExists($uploadedPath);
    }

    /** @test */
    public function itUploadsFileWithOriginalName(): void
    {
        Storage::fake();

        $file = $this->getUploadedFile();

        $path = 'uploads/images';

        $disk = 'public';

        $uploadedPath = MediaHelper::uploadFile(file: $file, path: $path, withOriginalName: true, disk: $disk);

        Storage::disk($disk)->assertExists($uploadedPath);
        Storage::disk($disk)->assertExists($path . '/test1.jpg');
    }

    /** @test */
    public function itUploadsFileWithCustomName(): void
    {
        Storage::fake();

        $file = $this->getUploadedFile();

        $path = 'uploads/images';

        $customFileName = 'custom_name.jpg';

        $disk = 'public';

        $uploadedPath = MediaHelper::uploadFile(file: $file, path: $path, fileName: $customFileName, disk: $disk);

        Storage::disk($disk)->assertExists($uploadedPath);
        Storage::disk($disk)->assertExists($path . '/' . $customFileName);
    }

    /** @test */
    public function itUploadsMultipleFiles(): void
    {
        Storage::fake();

        $files = [
            $this->getUploadedFile('test1.jpg'),
            $this->getUploadedFile('test2.jpg'),
        ];

        $path = 'uploads/images';

        $disk = 'public';

        $uploadedPaths = MediaHelper::uploadMultiple(files: $files, path: $path, disk: $disk);

        foreach ($uploadedPaths as $uploadedPath) {
            Storage::disk($disk)->assertExists($uploadedPath);
        }
    }

    /** @test */
    public function itUploadsMultipleFilesWithCustomNames(): void
    {
        Storage::fake();

        $files = [
            $this->getUploadedFile('test1.jpg'),
            $this->getUploadedFile('test2.jpg'),
        ];

        $path = 'uploads/images';

        $customFileNames = ['custom_name_1.jpg', 'custom_name_2.jpg'];

        $disk = 'public';

        $uploadedPaths = MediaHelper::uploadMultiple(files: $files, path: $path, filesNames: $customFileNames, disk: $disk);

        foreach ($uploadedPaths as $uploadedPath) {
            Storage::disk($disk)->assertExists($uploadedPath);
        }
        foreach ($customFileNames as $customFileName) {
            Storage::disk($disk)->assertExists($path . '/' . $customFileName);
        }
    }

    /** @test */
    public function itUploadsBase64Image(): void
    {
        Storage::fake();

        $base64Image = self::BASE_64_IMAGE;

        $path = 'uploads/images';

        $disk = 'public';

        $uploadedPath = MediaHelper::uploadBase64Image(decodedFile:  $base64Image, path: $path, disk: $disk);

        Storage::disk($disk)->assertExists($uploadedPath);
    }

    /** @test */
    public function itUploadsBase64ImageWithCustomName(): void
    {
        Storage::fake();

        $base64Image = self::BASE_64_IMAGE;

        $path = 'uploads/images';

        $customFileName = 'custom_image.png';

        $disk = 'public';

        $uploadedPath = MediaHelper::uploadBase64Image(decodedFile: $base64Image, path: $path, fileName: $customFileName, disk: $disk);

        Storage::disk($disk)->assertExists($uploadedPath);
        Storage::disk($disk)->assertExists($path . '/' . $customFileName);
    }

    /** @test */
    public function itDeletesFile(): void
    {
        Storage::fake();

        $file = $this->getUploadedFile();

        $path = 'uploads/images';

        $disk = 'public';

        $uploadedPath = MediaHelper::uploadFile(file: $file, path: $path, disk: $disk);

        MediaHelper::deleteFile($uploadedPath);

        Storage::disk($disk)->assertMissing($uploadedPath);
    }

    /** @test */
    public function itGetsFileFullPath(): void
    {
        Storage::fake();

        $file = $this->getUploadedFile();

        $path = 'uploads/images';

        $disk = 'public';

        $uploadedPath = MediaHelper::uploadFile(file: $file, path: $path, disk: $disk);

        $fullPath = MediaHelper::getFileFullPath($uploadedPath);

        $this->assertEquals(Storage::disk($disk)->url($uploadedPath), $fullPath);
    }

    /** @test */
    public function itGetsNullFileFullPathForNullFilePath(): void
    {
        Storage::fake();

        $fileFullPath = MediaHelper::getFileFullPath(null);

        $this->assertNull($fileFullPath);
    }

    /** @test */
    public function itUploadsAndDeletesBase64Images(): void
    {
        Storage::fake();

        $base64Image = self::BASE_64_IMAGE;
        $path = 'uploads/images';
        $disk = 'public';
        $uploadedPath = MediaHelper::uploadBase64Image(decodedFile: $base64Image, path: $path, disk: $disk);
        Storage::disk($disk)->assertExists($uploadedPath);

        MediaHelper::deleteFile(filePath: $uploadedPath, disk: $disk);
        Storage::disk($disk)->assertMissing($uploadedPath);
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
