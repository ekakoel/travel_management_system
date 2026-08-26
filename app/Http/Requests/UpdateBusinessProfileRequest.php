<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBusinessProfileRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() !== null && $this->user()->canany(['posDev','posAdm']);
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'nickname' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:100'],
            'license' => ['nullable', 'string', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'map' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:100'],
            'phone_2' => ['nullable', 'string', 'max:100'],
            'phone_3' => ['nullable', 'string', 'max:100'],
            'whatsapp' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'twitter' => ['nullable', 'string', 'max:255'],
            'youtube' => ['nullable', 'string', 'max:255'],
            'linkedin' => ['nullable', 'string', 'max:255'],
            'public_tagline' => ['nullable', 'string', 'max:500'],
            'public_tagline_traditional' => ['nullable', 'string', 'max:500'],
            'public_tagline_simplified' => ['nullable', 'string', 'max:500'],
            'public_description' => ['nullable', 'string', 'max:5000'],
            'public_description_traditional' => ['nullable', 'string', 'max:5000'],
            'public_description_simplified' => ['nullable', 'string', 'max:5000'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'logo_dark' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
