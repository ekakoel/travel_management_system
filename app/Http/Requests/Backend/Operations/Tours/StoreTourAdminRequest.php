<?php

namespace App\Http\Requests\Backend\Operations\Tours;

use Illuminate\Foundation\Http\FormRequest;

class StoreTourAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('isAdmin') ?? false;
    }

    public function rules(): array
    {
        return [
            'cover' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:125',
            'name_traditional' => 'required|string|max:255',
            'name_simplified' => 'required|string|max:255',
            'type' => 'required|integer|exists:tour_types,id',
            'duration_days' => 'required|integer|min:1',
            'duration_nights' => 'required|integer|min:0',
            'short_description' => 'required|string',
            'short_description_traditional' => 'required|string',
            'short_description_simplified' => 'required|string',
            'description' => 'required|string',
            'description_traditional' => 'required|string',
            'description_simplified' => 'required|string',
            'package_highlights' => 'nullable|string',
            'package_highlights_traditional' => 'nullable|string',
            'package_highlights_simplified' => 'nullable|string',
            'itinerary' => 'required|string',
            'itinerary_traditional' => 'required|string',
            'itinerary_simplified' => 'required|string',
            'include' => 'required|string',
            'include_traditional' => 'required|string',
            'include_simplified' => 'required|string',
            'exclude' => 'required|string',
            'exclude_traditional' => 'required|string',
            'exclude_simplified' => 'required|string',
            'additional_info' => 'required|string',
            'additional_info_traditional' => 'required|string',
            'additional_info_simplified' => 'required|string',
            'cancellation_policy' => 'required|string',
            'cancellation_policy_traditional' => 'required|string',
            'cancellation_policy_simplified' => 'required|string',
        ];
    }
}
