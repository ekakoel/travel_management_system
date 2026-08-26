<?php

namespace App\Http\Requests\Backend\Operations\Transports;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateTransportAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user?->can('isAdmin') && ($user->can('posDev') || $user->can('posAuthor'));
    }

    public function rules(): array
    {
        return [
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:4096',
            'partner_id' => [
                'nullable',
                'integer',
                Rule::exists('partners', 'id')->where(function ($query) {
                    $query->where('status', '!=', 'Removed')
                        ->whereIn('type', ['Transport', 'Activity & Transport']);
                }),
            ],
            'name' => 'required|string|max:255',
            'type' => ['required', 'string', 'max:255', Rule::exists('transport_types', 'type')],
            'brand' => ['required', 'string', 'max:255', Rule::exists('transport_brands', 'brand')],
            'capacity' => 'required|integer|min:1',
            'inventory' => 'nullable|integer|min:0',
            'description' => 'required|string',
            'include' => 'required|string',
            'additional_info' => 'nullable|string',
            'cancellation_policy' => 'nullable|string',
            'status' => 'required|string|in:Active,Draft,Archived',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->input('status') === 'Active' && (int) $this->input('inventory', 0) < 1) {
                $validator->errors()->add('inventory', 'Active transports require inventory of at least 1.');
            }
        });
    }
}
