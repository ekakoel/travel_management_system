<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdditionalInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()
            && $this->user()->type === 'admin'
            && in_array($this->user()->position, ['developer', 'administrator', 'reservation', 'weddingRsv'], true);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => ['required', 'date_format:Y-m-d'],
            'description' => ['required', 'string', 'max:255'],
            'rate' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
            'unit' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'times' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
        ];
    }
}
