<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActivityAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canAny(['posDev', 'posAdm', 'posAuthor']) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'map' => ['nullable', 'string'],
            'type' => ['required', 'string', 'max:255'],
            'duration' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'description_traditional' => ['nullable', 'string'],
            'description_simplified' => ['nullable', 'string'],
            'itinerary' => ['nullable', 'string'],
            'itinerary_traditional' => ['nullable', 'string'],
            'itinerary_simplified' => ['nullable', 'string'],
            'include' => ['nullable', 'string'],
            'include_traditional' => ['nullable', 'string'],
            'include_simplified' => ['nullable', 'string'],
            'additional_info' => ['nullable', 'string'],
            'additional_info_traditional' => ['nullable', 'string'],
            'additional_info_simplified' => ['nullable', 'string'],
            'cancellation_policy' => ['nullable', 'string'],
            'cancellation_policy_traditional' => ['nullable', 'string'],
            'cancellation_policy_simplified' => ['nullable', 'string'],
            'contract_rate' => ['required', 'integer', 'min:1'],
            'markup' => ['nullable', 'integer', 'min:0'],
            'qty' => ['required', 'integer', 'min:1', 'gte:min_pax'],
            'min_pax' => ['required', 'integer', 'min:1'],
            'validity' => ['required', 'date'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'partners_id' => ['required', 'exists:partners,id'],
            'status' => ['required', 'in:Active,Draft,Archived'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'contract_rate' => $this->normalizeNumericInput($this->input('contract_rate')),
            'markup' => $this->normalizeNumericInput($this->input('markup')),
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($this->input('status') !== 'Active' || $validator->errors()->has('validity')) {
                return;
            }

            $validityTimestamp = strtotime((string) $this->input('validity'));

            if ($validityTimestamp !== false && $validityTimestamp < now()->startOfDay()->timestamp) {
                $validator->errors()->add(
                    'validity',
                    'An expired Activity cannot be published as Active.'
                );
            }
        });
    }

    private function normalizeNumericInput(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        return str_replace([',', '.'], '', $value);
    }
}
