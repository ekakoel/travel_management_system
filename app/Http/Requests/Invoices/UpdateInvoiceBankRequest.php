<?php

namespace App\Http\Requests\Invoices;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceBankRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()
            && $this->user()->type === 'admin'
            && in_array($this->user()->position, ['developer', 'administrator', 'reservation', 'weddingRsv'], true);
    }

    public function rules(): array
    {
        return [
            'bank_id' => ['required', 'integer', 'exists:bank_accounts,id'],
        ];
    }
}
