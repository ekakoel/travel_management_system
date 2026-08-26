<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Validator;

class UpdateHotelRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::any(['posDev', 'posAuthor', 'posAdm']);
    }

    public function rules(): array
    {
        return [
            'cover' => ['nullable', 'image', 'max:4096'],
            'rooms' => ['required', 'string', 'max:255'],
            'room_view' => ['required', 'string', 'max:100'],
            'custom_room_view' => ['required_if:room_view,custom', 'nullable', 'string', 'max:100'],
            'beds' => ['required', 'string', 'max:100'],
            'custom_beds' => ['required_if:beds,custom', 'nullable', 'string', 'max:100'],
            'size' => ['nullable', 'string', 'max:100'],
            'capacity_adult' => ['required', 'integer', 'min:1'],
            'capacity_child' => ['nullable', 'integer', 'min:0'],
            'inventory' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:Active,Draft,Archived'],
            'include' => ['nullable', 'string'],
            'include_traditional' => ['nullable', 'string'],
            'include_simplified' => ['nullable', 'string'],
            'amenities' => ['nullable', 'string'],
            'amenities_traditional' => ['nullable', 'string'],
            'amenities_simplified' => ['nullable', 'string'],
            'additional_info' => ['nullable', 'string'],
            'additional_info_traditional' => ['nullable', 'string'],
            'additional_info_simplified' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $status = strtolower((string) $this->input('status', 'Active'));
            $inventory = (int) $this->input('inventory', 0);

            if ($status === 'active' && $inventory < 1) {
                $validator->errors()->add('inventory', 'Active rooms must have at least one available room. Use a non-active status for stop-sell inventory.');
            }
        });
    }
}
