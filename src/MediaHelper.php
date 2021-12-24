<?php

namespace Essa\APIToolKit;

use Illuminate\Support\Facades\Storage;

class MediaHelper
{
    /**
     * @param      $file              [ file]
     * @param      $path
     * @param null $old_file          [file path] [delete old file if exist]
     * @param bool $with_original_name if you want to save file with its original data
     * @return string [file full path after being moved]
     */
    public static function uploadFile($file, $path, $old_file = null,bool $with_original_name = false): string
    {
        if (!is_null($old_file)) {
            self::deleteFile($old_file);
        }

        if ($with_original_name) {
            return Storage::putFileAs($path, $file, $file->getClientOriginalName());
        }

        return $file->store('/' . $path);
    }

    /**
     * upload multiple files
     * @param array $files
     * @param string $path
     * @param bool $with_original_names
     * @return array
     */
    public static function uploadMultiple(array $files, string $path, bool $with_original_names = false): array
    {
        $files_names = [];

        foreach ($files as $file) {
            $files_names[] = self::uploadFile($file, $path, $with_original_names);
        }

        return $files_names;
    }


    /**
     * @param $file
     * @param string $path
     * @param string|null $old_file
     * @return string
     */
    public static function uploadBase64Image($file, string $path, string $old_file = null): string
    {
        if (! is_null($old_file)) {
            self::deleteFile($old_file);
        }

        @list($type, $file_data) = explode(';', $file);

        @list(, $file_data) = explode(',', $file_data);

        $file_name = time().uniqid() . '.png';

        return Storage::put(
            $path . '/' . $file_name,
            base64_decode($file_data)
        );
    }

    /**
     * [deleteFile description]
     * @param  [string] $file [image path to be deleted]
     */
    public static function deleteFile($file)
    {
        if (Storage::exists($file)) {
            Storage::delete($file);
        }
    }
}
