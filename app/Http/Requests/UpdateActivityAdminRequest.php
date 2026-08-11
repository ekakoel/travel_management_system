<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActivityAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'map' => ['nullable', 'string'],
            'type' => ['required', 'string', 'max:255'],
            'duration' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'description_traditional' => ['required', 'string'],
            'description_simplified' => ['required', 'string'],
            'itinerary' => ['nullable', 'string'],
            'itinerary_traditional' => ['nullable', 'string'],
            'itinerary_simplified' => ['nullable', 'string'],
            'include' => ['nullable', 'string'],
            'include_traditional' => ['nullable', 'string'],
            'include_simplified' => ['nullable', 'string'],
            'additional_info' => ['nullable', 'string'],
            'additional_info_traditional' => ['nullable', 'string'],
            'additional_info_simplified' => ['nullable', 'string'],
            'cancellation_policy' => ['nullable', 'string'],
            'cancellation_policy_traditional' => ['nullable', 'string'],
            'cancellation_policy_simplified' => ['nullable', 'string'],
            'contract_rate' => ['required', 'numeric', 'min:0'],
            'markup' => ['nullable', 'numeric', 'min:0'],
            'qty' => ['required', 'integer', 'min:0'],
            'min_pax' => ['required', 'integer', 'min:0'],
            'validity' => ['required', 'date'],
            'cover' => ['nullable', 'image', 'max:4096'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:4096'],
            'partners_id' => ['required', 'exists:partners,id'],
            'status' => ['required', 'in:Active,Draft,Archived'],
            'author' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
