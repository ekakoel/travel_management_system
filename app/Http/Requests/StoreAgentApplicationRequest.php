<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgentApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'company_type' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'company_address' => ['required', 'string', 'max:500'],
            'website' => ['nullable', 'url', 'max:255'],
            'business_license_number' => ['nullable', 'string', 'max:100'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'position' => ['nullable', 'string', 'max:100'],
            'preferred_contact' => ['nullable', 'string', 'max:50'],
            'main_market' => ['nullable', 'string', 'max:255'],
            'monthly_bali_clients' => ['nullable', 'integer', 'min:0'],
            'interested_services' => ['nullable', 'array'],
            'interested_services.*' => ['string', 'max:100'],
            'business_license' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'company_letter' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'tax_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'supporting_documents' => ['nullable', 'array'],
            'supporting_documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'agree_to_terms' => ['required', 'accepted'],
            'agree_to_contact' => ['nullable', 'accepted'],
            'submission_token' => ['required', 'uuid'],
        ];
    }
}
