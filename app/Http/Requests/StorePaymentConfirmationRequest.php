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
            'receipt_name' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'mimetypes:image/jpeg,image/png,application/pdf', 'max:5120', new SafeReceiptUpload()],
        ];
    }
}
