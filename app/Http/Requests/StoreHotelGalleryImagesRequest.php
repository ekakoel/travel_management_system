<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreHotelGalleryImagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::any(['posDev', 'posAuthor', 'posAdm']);
    }

    public function rules(): array
    {
        return [
            'images' => ['required', 'array', 'min:1', 'max:12'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }
}
