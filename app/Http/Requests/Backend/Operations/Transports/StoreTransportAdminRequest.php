<?php

namespace App\Http\Requests\Backend\Operations\Transports;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreTransportAdminRequest extends FormRequest
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
            'type' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'inventory' => 'nullable|integer|min:0',
            'description' => 'required|string',
            'include' => 'required|string',
            'additional_info' => 'nullable|string',
            'cancellation_policy' => 'nullable|string',
            'status' => 'nullable|string|in:Active,Draft,Archived',
            'author' => 'required|integer',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->input('status', 'Draft') === 'Active' && (int) $this->input('inventory', 0) < 1) {
                $validator->errors()->add('inventory', 'Active transports require inventory of at least 1.');
            }
        });
    }
}
