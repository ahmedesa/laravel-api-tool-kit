<?php

namespace Essa\APIToolKit;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaHelper
{
    /**
     * @param  UploadedFile  $file [ file]
     * @param  string|null  $oldFilePath delete old file if exist
     * @param  bool  $withOriginalName if you want to save file with its original name
     * @return string file full path after being uploaded
     */
    public static function uploadFile(UploadedFile $file, string $path, ?string $oldFilePath = null, bool $withOriginalName = false): string
    {
        if (! is_null($oldFilePath)) {
            static::deleteFile($oldFilePath);
        }

        if ($withOriginalName) {
            return Storage::putFileAs($path, $file, $file->getClientOriginalName());
        }

        $fullFilePath = static::getBasePathPrefix() . $path;

        return $file->store($fullFilePath);
    }

    /**
     * upload multiple files
     */
    public static function uploadMultiple(array $files, string $path, bool $withOriginalNames = false): array
    {
        $filesNames = [];

        foreach ($files as $file) {
            $filesNames[] = static::uploadFile($file, $path, $withOriginalNames);
        }

        return $filesNames;
    }

    public static function uploadBase64Image(string $decodedFile, string $path, string $oldFilePath = null): string
    {
        if (!is_null($oldFilePath)) {
            static::deleteFile($oldFilePath);
        }

        @list($type, $fileData) = explode(';', $decodedFile);

        @list(, $fileData) = explode(',', $fileData);

        $fileName = time() . uniqid() . '.png';

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
