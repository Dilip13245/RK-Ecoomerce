<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    public static function upload($file, $directory, $nameSlug = null)
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        $slug = $nameSlug ? Str::slug($nameSlug) : Str::random(10);
        $timestamp = now()->format('Ymd_His');
        $random = Str::random(5);
        $extension = $file->getClientOriginalExtension();

        $imageName = "{$slug}_{$timestamp}_{$random}.{$extension}";

        $file->storeAs($directory, $imageName, 'public');

        return $imageName;
    }

    public static function uploadBanner($file, $directory)
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        // Generate timestamp in the same format as your product images
        $timestamp = time(); // Unix timestamp like 1751811388291
        $date = now()->format('Ymd'); // Date like 20250706
        $time = now()->format('His'); // Time like 141935
        $extension = $file->getClientOriginalExtension();

        $imageName = "{$timestamp}_{$date}_{$time}.{$extension}";

        $file->storeAs($directory, $imageName, 'public');

        return $imageName;
    }

    public static function uploadMultiple(array $files, string $directory, string $nameSlug = null): array
    {
        $imageNames = [];

        foreach ($files as $file) {
            if ($file && $file->isValid()) {
                $slug = $nameSlug ? Str::slug($nameSlug) : Str::random(10);
                $timestamp = now()->format('Ymd_His');
                $random = Str::random(5);
                $extension = $file->getClientOriginalExtension();

                $imageName = "{$slug}_{$timestamp}_{$random}.{$extension}";

                $file->storeAs($directory, $imageName, 'public');

                $imageNames[] = $imageName; 
            }
        }

        return $imageNames;
    }

    public static function deleteMultiple($directory, array $imageNames)
    {
        foreach ($imageNames as $imageName) {
            if ($imageName && Storage::disk('public')->exists("{$directory}/{$imageName}")) {
                Storage::disk('public')->delete("{$directory}/{$imageName}");
            }
        }
    }

    public static function delete($directory, $imageName)
    {
        if ($imageName && Storage::disk('public')->exists("{$directory}/{$imageName}")) {
            Storage::disk('public')->delete("{$directory}/{$imageName}");
        }
    }

    public static function getImageUrl($imageName, $directory)
    {
        if (!$imageName) {
            return null;
        }
        
        return asset("storage/{$directory}/{$imageName}");
    }
}

