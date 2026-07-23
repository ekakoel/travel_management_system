<?php

namespace App\Services\Transports;

use App\Models\TransportsImages;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TransportAssetService
{
    public const COVER_PATH = 'storage/transports/transports-cover';
    public const GALLERY_PATH = 'storage/transports/transports-gallery';

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

    public function uploadGallery(int $transportId, UploadedFile $file): TransportsImages
    {
        return TransportsImages::create([
            'transports_id' => $transportId,
            'image' => $this->storeImage($file, self::GALLERY_PATH),
        ]);
    }

    public function deleteGallery(?string $fileName): bool
    {
        return $this->delete(self::GALLERY_PATH, $fileName);
    }

    private function storeImage(UploadedFile $file, string $directory): string
    {
        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $baseName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $filename = time() . '_' . $baseName . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $filename);

        return $filename;
    }

    private function delete(string $directory, ?string $fileName): bool
    {
        if (! $fileName) {
            return false;
        }

        $path = $directory . '/' . $fileName;

        if (! File::exists($path)) {
            return false;
        }

        return File::delete($path);
    }
}
