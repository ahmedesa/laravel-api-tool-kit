<?php

namespace Essa\APIToolKit;

use Essa\APIToolKit\Enum\FileSystemDisk;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaHelper
{
    protected static string $disk = FileSystemDisk::PUBLIC_DISK_NAME;

    public function __construct()
    {
        self::$disk = config('filesystems.default') ?: self::$disk;
    }

    public static function disk(string $name): static
    {
        self::$disk = $name;
        return new self();
    }

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
     * upload multiple files
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

    public static function deleteFile(string $filePath): void
    {
        if (Storage::disk(self::$disk)->exists($filePath)) {
            Storage::disk(self::$disk)->delete($filePath);
        }
    }

    public static function getFileFullPath(?string $filePath): ?string
    {
        return null === $filePath ? null : Storage::disk(self::$disk)->url($filePath);
    }

    protected static function getBasePathPrefix(): string
    {
        return '/';
    }
}
