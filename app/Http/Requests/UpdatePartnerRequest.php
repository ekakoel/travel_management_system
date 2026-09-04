<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canAny(['posDev', 'posAdm', 'posAuthor']) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'map' => ['required', 'string'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'type' => ['required', 'string', 'in:Activity,Transport,F&B,Wellness'],
            'phone' => ['required', 'string', 'max:50'],
            'contact_person' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:Draft,Active'],
            'description' => ['nullable', 'string'],
        ];
    }
}
