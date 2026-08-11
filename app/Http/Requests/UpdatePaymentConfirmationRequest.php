<?php

namespace App\Http\Requests;

use App\Rules\SafeReceiptUpload;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentConfirmationRequest extends FormRequest
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
            'receipt_file' => ['required_without_all:activity_receipt_name,receipt_name', 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'mimetypes:image/jpeg,image/png,application/pdf', 'max:5120', new SafeReceiptUpload()],
            'activity_receipt_name' => ['required_without_all:receipt_file,receipt_name', 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'mimetypes:image/jpeg,image/png,application/pdf', 'max:5120', new SafeReceiptUpload()],
            'receipt_name' => ['required_without_all:receipt_file,activity_receipt_name', 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'mimetypes:image/jpeg,image/png,application/pdf', 'max:5120', new SafeReceiptUpload()],
        ];
    }

    public function attributes(): array
    {
        return [
            'receipt_file' => __('messages.Payment Proof'),
            'activity_receipt_name' => __('messages.Payment Proof'),
            'receipt_name' => __('messages.Payment Proof'),
        ];
    }
}
