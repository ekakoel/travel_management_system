<?php

namespace App\Services\Activities;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ActivityAssetService
{
    public const COVER_PATH = 'activities/activities-cover';
    public const GALLERY_PATH = 'activities/activities-images';

    public function upload(UploadedFile $file, string $directory): string
    {
        $fileName = time().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension();
        $file->storeAs($directory, $fileName, 'public');

        return $fileName;
    }

    public function uploadPath(UploadedFile $file, string $directory): string
    {
        return $directory.'/'.$this->upload($file, $directory);
    }

    public function replace(?string $currentFileName, UploadedFile $file, string $directory): string
    {
        $this->delete($currentFileName, $directory);

        return $this->upload($file, $directory);
    }

    public function delete(?string $fileName, string $directory): bool
    {
        if (! $fileName) {
            return false;
        }

        $path = $this->storagePath($directory, $fileName);

        if (! Storage::disk('public')->exists($path)) {
            return false;
        }

        return Storage::disk('public')->delete($path);
    }

    public function uploadCover(UploadedFile $file): string
    {
        return $this->upload($file, self::COVER_PATH);
    }

    public function replaceCover(?string $currentFileName, UploadedFile $file): string
    {
        return $this->replace($currentFileName, $file, self::COVER_PATH);
    }

    public function deleteCover(?string $fileName): bool
    {
        return $this->delete($fileName, self::COVER_PATH);
    }

    public function uploadGalleryImage(UploadedFile $file): string
    {
        return $this->uploadPath($file, self::GALLERY_PATH);
    }

    public function deleteGalleryImage(?string $fileName): bool
    {
        return $this->delete($fileName, self::GALLERY_PATH);
    }

    public function deleteStoredPath(?string $path): bool
    {
        if (! $path) {
            return false;
        }

        return Storage::disk('public')->delete(ltrim($path, '/'));
    }

    private function storagePath(string $directory, string $fileName): string
    {
        $fileName = ltrim($fileName, '/');

        if (Str::startsWith($fileName, 'storage/')) {
            return Str::after($fileName, 'storage/');
        }

        if (Str::startsWith($fileName, $directory.'/')) {
            return $fileName;
        }

        return $directory.'/'.$fileName;
    }
}
