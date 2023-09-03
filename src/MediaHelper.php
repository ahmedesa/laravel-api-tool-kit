<?php

namespace Essa\APIToolKit;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaHelper
{
    public static function uploadFile(
        UploadedFile $file,
        string $path,
        ?string $fileName = null,
        bool $withOriginalName = false,
        string $disk = "public"
    ): string {
        $fileName = $fileName ?: ($withOriginalName ? $file->getClientOriginalName() : $file->hashName());

        $fullFilePath = static::getBasePathPrefix() . $path;

        return Storage::disk($disk)->putFileAs($fullFilePath, $file, $fileName);

    }

    /**
     * upload multiple files
     */
    public static function uploadMultiple(
        array $files,
        string $path,
        ?array $filesNames = null,
        bool $withOriginalNames = false,
        string $disk = "public"
    ): array {
        $filesPaths = [];

        foreach ($files as $index => $file) {
            $fileName = $filesNames[$index] ?? null;

            $filesPaths[] = static::uploadFile(
                $file,
                $path,
                $fileName,
                $withOriginalNames,
                $disk
            );
        }

        return $filesPaths;
    }

    public static function uploadBase64Image(
        string $decodedFile,
        string $path,
        ?string $fileName = null,
        string $disk = "public"
    ): string {
        @[$type, $fileData] = explode(';', $decodedFile);

        @[, $fileData] = explode(',', $fileData);

        $fileName = $fileName ?: time() . uniqid('', true) . '.png';

        $fullFilePath = static::getBasePathPrefix() . $path . '/' . $fileName;

        Storage::disk($disk)->put(
            $fullFilePath,
            base64_decode($fileData)
        );

        return $fullFilePath;
    }

    public static function deleteFile(string $filePath, string $disk = "public"): void
    {
        if (Storage::disk($disk)->exists($filePath)) {
            Storage::disk($disk)->delete($filePath);
        }
    }

    public static function getFileFullPath(?string $filePath, string $disk = "public"): ?string
    {
        return null === $filePath ? null : Storage::disk($disk)->url($filePath);
    }

    protected static function getBasePathPrefix(): string
    {
        return '/';
    }
}
