<?php

namespace Essa\APIToolKit\Tests;

use Essa\APIToolKit\MediaHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class MediaHelperTest extends TestCase
{
    public const BASE_64_IMAGE = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/w8AAwAB/AL+f4R4AAAAASUVORK5CYII=';

    private string $testingImage = __DIR__ . '/Images/laravel-api-tool-kit.png';

    /** @test */
    public function itUploadsFile(): void
    {
        Storage::fake('public');

        $file = $this->getUploadedFile();

        $path = 'uploads/images';

        $disk = 'public';

        $uploadedPath = MediaHelper::disk($disk)->uploadFile(file: $file, path: $path);

        Storage::disk($disk)->assertExists($uploadedPath);
    }

    /** @test */
    public function itUploadsFileWithoutDisk(): void
    {
        Storage::fake('public');
        Config::set('filesystems.default', 'public');

        $file = $this->getUploadedFile();

        $path = 'uploads/images';

        $disk = 'public';

        $uploadedPath = MediaHelper::uploadFile(file: $file, path: $path);

        Storage::disk($disk)->assertExists($uploadedPath);
    }

    /** @test */
    public function itUploadsFileWithOriginalName(): void
    {
        Storage::fake('public');

        $file = $this->getUploadedFile();

        $path = 'uploads/images';

        $disk = 'public';

        $uploadedPath = MediaHelper::disk($disk)->uploadFile(file: $file, path: $path, withOriginalName: true);

        Storage::disk($disk)->assertExists($uploadedPath);
        Storage::disk($disk)->assertExists($path . '/test1.jpg');
    }

    /** @test */
    public function itUploadsFileWithOriginalNameWithoutDisk(): void
    {
        Storage::fake('public');
        Config::set('filesystems.default', 'public');

        $file = $this->getUploadedFile();

        $path = 'uploads/images';

        $disk = 'public';

        $uploadedPath = MediaHelper::uploadFile(file: $file, path: $path, withOriginalName: true);

        Storage::disk($disk)->assertExists($uploadedPath);
        Storage::disk($disk)->assertExists($path . '/test1.jpg');
    }

    /** @test */
    public function itUploadsFileWithCustomName(): void
    {
        Storage::fake('public');

        $file = $this->getUploadedFile();

        $path = 'uploads/images';

        $customFileName = 'custom_name.jpg';

        $disk = 'public';

        $uploadedPath = MediaHelper::disk($disk)->uploadFile(file: $file, path: $path, fileName: $customFileName);

        Storage::disk($disk)->assertExists($uploadedPath);
        Storage::disk($disk)->assertExists($path . '/' . $customFileName);
    }

    /** @test */
    public function itUploadsFileWithCustomNameWithoutDisk(): void
    {
        Storage::fake('public');
        Config::set('filesystems.default', 'public');

        $file = $this->getUploadedFile();

        $path = 'uploads/images';

        $customFileName = 'custom_name.jpg';

        $disk = 'public';

        $uploadedPath = MediaHelper::uploadFile(file: $file, path: $path, fileName: $customFileName);

        Storage::disk($disk)->assertExists($uploadedPath);
        Storage::disk($disk)->assertExists($path . '/' . $customFileName);
    }

    /** @test */
    public function itUploadsMultipleFiles(): void
    {
        Storage::fake('public');

        $files = [
            $this->getUploadedFile('test1.jpg'),
            $this->getUploadedFile('test2.jpg'),
        ];

        $path = 'uploads/images';

        $disk = 'public';

        $uploadedPaths = MediaHelper::disk($disk)->uploadMultiple(files: $files, path: $path);

        foreach ($uploadedPaths as $uploadedPath) {
            Storage::disk($disk)->assertExists($uploadedPath);
        }
    }

    /** @test */
    public function itUploadsMultipleFilesWithoutDisk(): void
    {
        Storage::fake('public');
        Config::set('filesystems.default', 'public');

        $files = [
            $this->getUploadedFile('test1.jpg'),
            $this->getUploadedFile('test2.jpg'),
        ];

        $path = 'uploads/images';

        $disk = 'public';

        $uploadedPaths = MediaHelper::uploadMultiple(files: $files, path: $path);

        foreach ($uploadedPaths as $uploadedPath) {
            Storage::disk($disk)->assertExists($uploadedPath);
        }
    }

    /** @test */
    public function itUploadsMultipleFilesWithCustomNames(): void
    {
        Storage::fake('public');

        $files = [
            $this->getUploadedFile('test1.jpg'),
            $this->getUploadedFile('test2.jpg'),
        ];

        $path = 'uploads/images';

        $customFileNames = ['custom_name_1.jpg', 'custom_name_2.jpg'];

        $disk = 'public';

        $uploadedPaths = MediaHelper::disk($disk)->uploadMultiple(files: $files, path: $path, filesNames: $customFileNames);

        foreach ($uploadedPaths as $uploadedPath) {
            Storage::disk($disk)->assertExists($uploadedPath);
        }
        foreach ($customFileNames as $customFileName) {
            Storage::disk($disk)->assertExists($path . '/' . $customFileName);
        }
    }

    /** @test */
    public function itUploadsMultipleFilesWithCustomNamesWithoutDisk(): void
    {
        Storage::fake('public');
        Config::set('filesystems.default', 'public');

        $files = [
            $this->getUploadedFile('test1.jpg'),
            $this->getUploadedFile('test2.jpg'),
        ];

        $path = 'uploads/images';

        $customFileNames = ['custom_name_1.jpg', 'custom_name_2.jpg'];

        $disk = 'public';

        $uploadedPaths = MediaHelper::uploadMultiple(files: $files, path: $path, filesNames: $customFileNames);

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
        Storage::fake('public');

        $base64Image = self::BASE_64_IMAGE;

        $path = 'uploads/images';

        $disk = 'public';

        $uploadedPath = MediaHelper::disk($disk)->uploadBase64Image(decodedFile:  $base64Image, path: $path);

        Storage::disk($disk)->assertExists($uploadedPath);
    }

    /** @test */
    public function itUploadsBase64ImageWithoutDisk(): void
    {
        Storage::fake('public');
        Config::set('filesystems.default', 'public');

        $base64Image = self::BASE_64_IMAGE;

        $path = 'uploads/images';

        $disk = 'public';

        $uploadedPath = MediaHelper::uploadBase64Image(decodedFile:  $base64Image, path: $path);

        Storage::disk($disk)->assertExists($uploadedPath);
    }

    /** @test */
    public function itUploadsBase64ImageWithCustomName(): void
    {
        Storage::fake('public');

        $base64Image = self::BASE_64_IMAGE;

        $path = 'uploads/images';

        $customFileName = 'custom_image.png';

        $disk = 'public';

        $uploadedPath = MediaHelper::disk($disk)->uploadBase64Image(decodedFile: $base64Image, path: $path, fileName: $customFileName);

        Storage::disk($disk)->assertExists($uploadedPath);
        Storage::disk($disk)->assertExists($path . '/' . $customFileName);
    }

    /** @test */
    public function itUploadsBase64ImageWithCustomNameWithoutDisk(): void
    {
        Storage::fake('public');
        Config::set('filesystems.default', 'public');

        $base64Image = self::BASE_64_IMAGE;

        $path = 'uploads/images';

        $customFileName = 'custom_image.png';

        $disk = 'public';

        $uploadedPath = MediaHelper::uploadBase64Image(decodedFile: $base64Image, path: $path, fileName: $customFileName);

        Storage::disk($disk)->assertExists($uploadedPath);
        Storage::disk($disk)->assertExists($path . '/' . $customFileName);
    }

    /** @test */
    public function itDeletesFile(): void
    {
        Storage::fake('public');

        $file = $this->getUploadedFile();

        $path = 'uploads/images';

        $disk = 'public';

        $uploadedPath = MediaHelper::disk($disk)->uploadFile(file: $file, path: $path);

        MediaHelper::disk($disk)->deleteFile($uploadedPath);

        Storage::disk($disk)->assertMissing($uploadedPath);
    }

    /** @test */
    public function itDeletesFileWithoutDisk(): void
    {
        Storage::fake('public');
        Config::set('filesystems.default', 'public');

        $file = $this->getUploadedFile();

        $path = 'uploads/images';

        $disk = 'public';

        $uploadedPath = MediaHelper::uploadFile(file: $file, path: $path);

        MediaHelper::deleteFile($uploadedPath);

        Storage::disk($disk)->assertMissing($uploadedPath);
    }

    /** @test */
    public function itGetsFileFullPath(): void
    {
        Storage::fake('public');

        $file = $this->getUploadedFile();

        $path = 'uploads/images';

        $disk = 'public';

        $uploadedPath = MediaHelper::disk($disk)->uploadFile(file: $file, path: $path);

        $fullPath = MediaHelper::getFileFullPath($uploadedPath);

        $this->assertEquals(Storage::disk($disk)->url($uploadedPath), $fullPath);
    }

    /** @test */
    public function itGetsFileFullPathWithoutDisk(): void
    {
        Storage::fake('public');
        Config::set('filesystems.default', 'public');

        $file = $this->getUploadedFile();

        $path = 'uploads/images';

        $disk = 'public';

        $uploadedPath = MediaHelper::uploadFile(file: $file, path: $path);

        $fullPath = MediaHelper::getFileFullPath($uploadedPath);

        $this->assertEquals(Storage::disk($disk)->url($uploadedPath), $fullPath);
    }

    /** @test */
    public function itGetsNullFileFullPathForNullFilePath(): void
    {
        Storage::fake('public');
        Config::set('filesystems.default', 'public');

        $fileFullPath = MediaHelper::getFileFullPath(null);

        $this->assertNull($fileFullPath);
    }

    /** @test */
    public function itUploadsAndDeletesBase64Images(): void
    {
        Storage::fake('public');

        $base64Image = self::BASE_64_IMAGE;
        $path = 'uploads/images';
        $disk = 'public';
        $uploadedPath = MediaHelper::disk($disk)->uploadBase64Image(decodedFile: $base64Image, path: $path);
        Storage::disk($disk)->assertExists($uploadedPath);

        MediaHelper::disk($disk)->deleteFile(filePath: $uploadedPath);
        Storage::disk($disk)->assertMissing($uploadedPath);
    }

    /** @test */
    public function itUploadsAndDeletesBase64ImagesWithoutDisk(): void
    {
        Storage::fake('public');
        Config::set('filesystems.default', 'public');

        $base64Image = self::BASE_64_IMAGE;
        $path = 'uploads/images';
        $disk = 'public';
        $uploadedPath = MediaHelper::uploadBase64Image(decodedFile: $base64Image, path: $path);
        Storage::disk($disk)->assertExists($uploadedPath);

        MediaHelper::deleteFile(filePath: $uploadedPath);
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
