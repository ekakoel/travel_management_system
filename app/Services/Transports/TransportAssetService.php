<?php

namespace App\Services\Transports;

use App\Models\TransportsImages;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TransportAssetService
{
    public const COVER_PATH = 'transports/transports-cover';
    public const GALLERY_PATH = 'transports/transports-gallery';

    public function uploadCover(UploadedFile $file): string
    {
        return $this->storeImage($file, self::COVER_PATH);
    }

    public function replaceCover(?string $currentFileName, UploadedFile $file): string
    {
        return $this->uploadCover($file);
    }

    public function deleteCover(?string $fileName): bool
    {
        return $this->delete(self::COVER_PATH, $fileName);
    }

    public function uploadGallery(int $transportId, UploadedFile $file): TransportsImages
    {
        $fileName = $this->storeImage($file, self::GALLERY_PATH);

        try {
            return TransportsImages::create([
                'transports_id' => $transportId,
                'image' => $fileName,
            ]);
        } catch (\Throwable $exception) {
            $this->deleteGallery($fileName);

            throw $exception;
        }
    }

    public function deleteGallery(?string $fileName): bool
    {
        return $this->delete(self::GALLERY_PATH, $fileName);
    }

    private function storeImage(UploadedFile $file, string $directory): string
    {
        $filename = Str::uuid() . '.' . $file->extension();
        $file->storeAs($directory, $filename, 'public');

        return $filename;
    }

    private function delete(string $directory, ?string $fileName): bool
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

    private function storagePath(string $directory, string $fileName): string
    {
        $fileName = ltrim($fileName, '/');

        if (Str::startsWith($fileName, 'storage/')) {
            return Str::after($fileName, 'storage/');
        }

        if (Str::startsWith($fileName, $directory . '/')) {
            return $fileName;
        }

        return $directory . '/' . $fileName;
    }
}
