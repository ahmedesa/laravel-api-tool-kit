<?php

namespace Essa\APIToolKit;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaHelper
{
    protected static ?string $disk = null;

    /**
     * Uploads a file to the specified path on the configured storage disk.
     *
     * @param UploadedFile $file The file to be uploaded.
     * @param string $path The path within the disk where the file will be stored.
     * @param string|null $fileName The optional custom name for the uploaded file.
     * @param bool $withOriginalName Whether to use the original file name if no custom name is provided.
     * @return string The full path of the uploaded file.
     */
    public static function uploadFile(
        UploadedFile $file,
        string $path,
        ?string $fileName = null,
        bool $withOriginalName = false
    ): string {
        $fileName = $fileName ?: ($withOriginalName ? $file->getClientOriginalName() : $file->hashName());

        $fullFilePath = static::getBasePathPrefix() . $path;

        return Storage::disk(self::$disk)->putFileAs($fullFilePath, $file, $fileName);
    }

    /**
     * Uploads multiple files to the specified path on the configured storage disk.
     *
     * @param array $files An array of UploadedFile instances to be uploaded.
     * @param string $path The path within the disk where the files will be stored.
     * @param array|null $filesNames An optional array of custom file names for the uploaded files.
     * @param bool $withOriginalNames Whether to use the original file names if no custom names are provided.
     * @return array An array of full paths of the uploaded files.
     */
    public static function uploadMultiple(
        array $files,
        string $path,
        ?array $filesNames = null,
        bool $withOriginalNames = false
    ): array {
        $filesPaths = [];

        foreach ($files as $index => $file) {
            $fileName = $filesNames[$index] ?? null;

            $filesPaths[] = static::uploadFile(
                $file,
                $path,
                $fileName,
                $withOriginalNames
            );
        }

        return $filesPaths;
    }

    /**
     * Uploads a base64-encoded image to the specified path on the configured storage disk.
     *
     * @param string $decodedFile The base64-encoded image data.
     * @param string $path The path within the disk where the image will be stored.
     * @param string|null $fileName The optional custom name for the uploaded image.
     * @return string The full path of the uploaded image.
     */
    public static function uploadBase64Image(
        string $decodedFile,
        string $path,
        ?string $fileName = null,
    ): string {
        @[$type, $fileData] = explode(';', $decodedFile);

        @[, $fileData] = explode(',', $fileData);

        $fileName = $fileName ?: time() . uniqid('', true) . '.png';

        $fullFilePath = static::getBasePathPrefix() . $path . '/' . $fileName;

        Storage::disk(self::$disk)->put(
            $fullFilePath,
            base64_decode($fileData)
        );

        return $fullFilePath;
    }

    /**
     * Deletes a file from the storage disk if it exists.
     *
     * @param string $filePath The path of the file to be deleted.
     * @return void
     */
    public static function deleteFile(string $filePath): void
    {
        if (Storage::disk(self::$disk)->exists($filePath)) {
            Storage::disk(self::$disk)->delete($filePath);
        }
    }

    /**
     * Gets the full URL of a file on the storage disk.
     *
     * @param string|null $filePath The path of the file.
     * @return string|null The full URL of the file, or null if the file path is null.
     */
    public static function getFileFullPath(?string $filePath): ?string
    {
        return null === $filePath ? null : Storage::disk(self::$disk)->url($filePath);
    }

    /**
     * Sets the storage disk to be used for file operations.
     *
     * @param string $name The name of the storage disk to use.
     * @return static A new instance of the MediaHelper class with the specified disk configuration.
     */
    public static function disk(string $name): static
    {
        self::$disk = $name;

        return new self();
    }

    /**
     * Returns the base path prefix for file storage within the configured disk.
     *
     * @return string The base path prefix.
     */
    protected static function getBasePathPrefix(): string
    {
        return '/';
    }
}
