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
            self::deleteFile($oldFilePath);
        }

        if ($withOriginalName) {
            return Storage::putFileAs($path, $file, $file->getClientOriginalName());
        }

        return $file->store('/' . $path);
    }

    /**
     * upload multiple files
     */
    public static function uploadMultiple(array $files, string $path, bool $withOriginalNames = false): array
    {
        $filesNames = [];

        foreach ($files as $file) {
            $filesNames[] = self::uploadFile($file, $path, $withOriginalNames);
        }

        return $filesNames;
    }

    public static function uploadBase64Image(string $decodedFile, string $path, string $oldFilePath = null): string
    {
        if (! is_null($oldFilePath)) {
            self::deleteFile($oldFilePath);
        }

        @[$type, $file_data] = explode(';', $decodedFile);

        @[, $file_data] = explode(',', $file_data);

        $file_name = time() . uniqid() . '.png';

        return Storage::put(
            $path . '/' . $file_name,
            base64_decode($file_data)
        );
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
}
