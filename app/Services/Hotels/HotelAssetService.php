<?php

namespace App\Services\Hotels;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class HotelAssetService
{
    public const COVER_PATH = 'storage/hotels/hotels-cover/';
    public const ROOM_PATH = 'storage/hotels/hotels-room/';
    public const GALLERY_PATH = 'storage/hotels/hotels-galery/';
    public const CONTRACT_PATH = 'storage/hotels/hotels-contract/';

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

    public function uploadHotelCover(UploadedFile $file): string
    {
        return $this->upload($file, self::COVER_PATH);
    }

    public function replaceHotelCover(?string $currentFileName, UploadedFile $file): string
    {
        return $this->replace($currentFileName, $file, self::COVER_PATH);
    }

    public function deleteHotelCover(?string $fileName): bool
    {
        return $this->delete($fileName, self::COVER_PATH);
    }

    public function uploadRoomCover(UploadedFile $file): string
    {
        return $this->upload($file, self::ROOM_PATH);
    }

    public function replaceRoomCover(?string $currentFileName, UploadedFile $file): string
    {
        return $this->replace($currentFileName, $file, self::ROOM_PATH);
    }

    public function deleteRoomCover(?string $fileName): bool
    {
        return $this->delete($fileName, self::ROOM_PATH);
    }

    public function deleteGalleryImage(?string $fileName): bool
    {
        return $this->delete($fileName, self::GALLERY_PATH);
    }
}
