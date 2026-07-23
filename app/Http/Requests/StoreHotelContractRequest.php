<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHotelContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contract_name' => ['required', 'string', 'max:255'],
            'hotels_id' => ['required', 'integer', 'exists:hotels,id'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'file_name' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }
}
