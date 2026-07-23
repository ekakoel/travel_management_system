<?php

namespace App\Http\Requests\Backend\Operations\Tours;

use Illuminate\Foundation\Http\FormRequest;

class StoreTourPriceAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('isAdmin') ?? false;
    }

    public function rules(): array
    {
        return [
            'min_qty' => 'required|integer|min:1',
            'max_qty' => 'required|integer|gte:min_qty',
            'contract_rate' => 'required|numeric|min:1',
            'markup' => 'required|numeric|min:0',
            'expired_date' => 'required|date',
        ];
    }
}
