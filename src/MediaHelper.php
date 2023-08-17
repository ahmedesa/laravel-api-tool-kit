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
        bool $withOriginalName = false
    ): string {
        $fileName = $fileName ?: ($withOriginalName ? $file->getClientOriginalName() : $file->hashName());

        $fullFilePath = static::getBasePathPrefix() . $path;

        return $file->storeAs($fullFilePath, $fileName);
    }

    /**
     * upload multiple files
     */
    public static function uploadMultiple(
        array $files,
        string $path,
        ?array $filesNames = null, bool $withOriginalNames = false
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
        ?string $fileName = null
    ): string {
        @[$type, $fileData] = explode(';', $decodedFile);

        @[, $fileData] = explode(',', $fileData);

        $fileName = $fileName ?: time() . uniqid() . '.png';

        $fullFilePath = static::getBasePathPrefix() . $path . '/' . $fileName;

        Storage::put(
            $fullFilePath,
            base64_decode($fileData)
        );

        return $fullFilePath;
    }

    public static function deleteFile(string $filePath): void
    {
        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }
    }

    public static function getFileFullPath(?string $filePath): ?string
    {
        return is_null($filePath) ? null : Storage::url($filePath);
    }

    protected static function getBasePathPrefix(): string
    {
        return '/';
    }
}
