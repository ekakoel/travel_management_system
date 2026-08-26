<?php

namespace App\Http\Requests\Backend\Operations\Transports;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransportAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user?->can('isAdmin') && ($user->can('posDev') || $user->can('posAuthor'));
    }

    public function rules(): array
    {
        return [
            'cover' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
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
        ];
    }

}
