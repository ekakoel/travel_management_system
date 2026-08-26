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
            'include' => 'nullable|string',
            'include_traditional' => 'nullable|string',
            'include_simplified' => 'nullable|string',
            'exclude' => 'nullable|string',
            'exclude_traditional' => 'nullable|string',
            'exclude_simplified' => 'nullable|string',
            'additional_info' => 'nullable|string',
            'additional_info_traditional' => 'nullable|string',
            'additional_info_simplified' => 'nullable|string',
            'cancellation_policy' => 'required|string',
            'cancellation_policy_traditional' => 'required|string',
            'cancellation_policy_simplified' => 'required|string',
        ];
    }
}
