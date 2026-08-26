<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateHotelAdditionalChargeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::any(['posDev', 'posAuthor', 'posAdm']);
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'description_traditional' => ['nullable', 'string'],
            'description_simplified' => ['nullable', 'string'],
            'mandatory' => ['required', 'boolean'],
            'mandatory_start' => ['required_if:mandatory,1', 'nullable', 'date'],
            'mandatory_end' => ['required_if:mandatory,1', 'nullable', 'date', 'after_or_equal:mandatory_start'],
            'markup' => ['required', 'numeric', 'min:0'],
            'contract_rate' => ['required', 'numeric', 'min:0'],
        ];
    }
}
