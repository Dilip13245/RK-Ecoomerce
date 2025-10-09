<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileHelper
{
    /**
     * Upload image to storage
     *
     * @param UploadedFile $file
     * @param string $path
     * @return string
     */
    public static function uploadImage(UploadedFile $file, string $path): string
    {
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        
        $file->storeAs('public/' . $path, $filename);
        
        return $filename;
    }

    /**
     * Delete file from storage
     *
     * @param string $path
     * @param string $filename
     * @return bool
     */
    public static function deleteFile(string $path, string $filename): bool
    {
        if (Storage::exists('public/' . $path . '/' . $filename)) {
            return Storage::delete('public/' . $path . '/' . $filename);
        }
        
        return false;
    }
}