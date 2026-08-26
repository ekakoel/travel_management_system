<?php

namespace App\Services\Tours;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TourAssetService
{
    public const COVER_PATH = 'tours/tours-cover';
    public const MARKER_PATH = 'tours/tour-location-markers';

    public function uploadCover(UploadedFile $file): string
    {
        return $this->storeImage($file, self::COVER_PATH);
    }

    public function replaceCover(?string $currentFileName, UploadedFile $file): string
    {
        $this->deleteCover($currentFileName);

        return $this->uploadCover($file);
    }

    public function deleteCover(?string $fileName): bool
    {
        return $this->delete(self::COVER_PATH, $fileName);
    }

    public function uploadMarker(UploadedFile $file): string
    {
        return $this->storeImage($file, self::MARKER_PATH, true);
    }

    public function deleteMarker(?string $fileName): bool
    {
        return $this->delete(self::MARKER_PATH, $fileName);
    }

    private function storeImage(UploadedFile $file, string $directory, bool $randomName = false): string
    {
        $baseName = $randomName
            ? Str::random(10)
            : Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $filename = time() . '_' . $baseName . '.' . $file->getClientOriginalExtension();
        $file->storeAs($directory, $filename, 'public');

        return $filename;
    }

    private function delete(string $directory, ?string $fileName): bool
    {
        if (! $fileName) {
            return false;
        }

        $path = $directory . '/' . $fileName;

        if (! Storage::disk('public')->exists($path)) {
            return false;
        }

        return Storage::disk('public')->delete($path);
    }
}
