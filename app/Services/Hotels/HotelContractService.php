<?php

namespace App\Services\Hotels;

use App\Models\Contract;
use Illuminate\Http\UploadedFile;

class HotelContractService
{
    public function __construct(private readonly HotelAssetService $assets)
    {
    }

    public function upload(UploadedFile $file): string
    {
        return $this->assets->upload($file, HotelAssetService::CONTRACT_PATH);
    }

    public function replace(Contract $contract, UploadedFile $file): string
    {
        return $this->assets->replace($contract->file_name, $file, HotelAssetService::CONTRACT_PATH);
    }

    public function delete(Contract $contract): bool
    {
        return $this->assets->delete($contract->file_name, HotelAssetService::CONTRACT_PATH);
    }

    public function previewUrl(Contract $contract): string
    {
        return asset(HotelAssetService::CONTRACT_PATH.$contract->file_name);
    }
}
