<?php

namespace Essa\APIToolKit;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaHelper
{
    /**
     * @param UploadedFile $file [ file]
     * @param string $path
     * @param string|null $oldFilePath delete old file if exist
     * @param bool $withOriginalName if you want to save file with its original name
     * @return string file full path after being uploaded
     */
    public static function uploadFile(UploadedFile $file, string $path, ?string $oldFilePath = null, bool $withOriginalName = false): string
    {
        if (!is_null($oldFilePath)) {
            self::deleteFile($oldFilePath);
        }

        if ($withOriginalName) {
            return Storage::putFileAs($path, $file, $file->getClientOriginalName());
        }

        return $file->store('/' . $path);
    }

    /**
     * upload multiple files
     * @param array $files
     * @param string $path
     * @param bool $withOriginalNames
     * @return array
     */
    public static function uploadMultiple(array $files, string $path, bool $withOriginalNames = false): array
    {
        $files_names = [];

        foreach ($files as $file) {
            $files_names[] = self::uploadFile($file, $path, $withOriginalNames);
        }

        return $files_names;
    }


    /**
     * @param string $decodedFile
     * @param string $path
     * @param string|null $oldFilePath
     * @return string
     */
    public static function uploadBase64Image(string $decodedFile, string $path, string $oldFilePath = null): string
    {
        if (!is_null($oldFilePath)) {
            self::deleteFile($oldFilePath);
        }

        @list($type, $file_data) = explode(';', $decodedFile);

        @list(, $file_data) = explode(',', $file_data);

        $file_name = time() . uniqid() . '.png';

        return Storage::put(
            $path . '/' . $file_name,
            base64_decode($file_data)
        );
    }

    /**
     * @param string $filePath
     */
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
