<?php

namespace App\Http\Requests;

use App\Rules\SafeReceiptUpload;
use Illuminate\Foundation\Http\FormRequest;

class StorePaymentConfirmationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'payment_standard_version' => ['nullable', 'in:1'],
            'payment_date' => ['required_if:payment_standard_version,1', 'nullable', 'date_format:Y-m-d', 'before_or_equal:today'],
            'amount_paid' => ['required_if:payment_standard_version,1', 'nullable', 'numeric', 'gt:0', 'max:999999999999.99'],
            'receipt_file' => ['required_without:receipt_name', 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'mimetypes:image/jpeg,image/png,application/pdf', 'max:5120', new SafeReceiptUpload()],
            'receipt_name' => ['required_without:receipt_file', 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'mimetypes:image/jpeg,image/png,application/pdf', 'max:5120', new SafeReceiptUpload()],
        ];
    }

    public function attributes(): array
    {
        return [
            'payment_date' => __('messages.Payment Date'),
            'amount_paid' => __('messages.Amount Paid'),
            'receipt_file' => __('messages.Payment Proof'),
            'receipt_name' => __('messages.Payment Proof'),
        ];
    }
}
