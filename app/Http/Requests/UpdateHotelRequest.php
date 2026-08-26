<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHotelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'region' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'description' => ['required', 'string'],
            'phone' => ['required', 'string', 'max:100'],
            'web' => ['required', 'string', 'max:255'],
            'cover' => ['nullable', 'image', 'max:4096'],
            'airport_duration' => ['required', 'string', 'max:100'],
            'airport_distance' => ['required', 'string', 'max:100'],
            'min_stay' => ['nullable', 'integer', 'min:0'],
            'max_stay' => ['nullable', 'integer', 'min:0', 'gte:min_stay'],
            'map' => ['required', 'string'],
            'status' => ['required', 'in:Active,Draft,Archived'],
        ];
    }
}
