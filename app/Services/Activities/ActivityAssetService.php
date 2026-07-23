<?php

namespace App\Services\Activities;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class ActivityAssetService
{
    public const COVER_PATH = 'storage/activities/activities-cover/';
    public const GALLERY_PATH = 'storage/activities/activities-images/';

    public function upload(UploadedFile $file, string $directory): string
    {
        $fileName = time().'_'.$file->getClientOriginalName();
        $file->move($directory, $fileName);

        return $fileName;
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

        $path = $directory.$fileName;

        if (! File::exists($path)) {
            return false;
        }

        return File::delete($path);
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
        return $this->upload($file, self::GALLERY_PATH);
    }

    public function deleteGalleryImage(?string $fileName): bool
    {
        return $this->delete($fileName, self::GALLERY_PATH);
    }
}
