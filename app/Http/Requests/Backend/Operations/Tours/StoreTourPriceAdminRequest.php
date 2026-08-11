<?php

namespace App\Http\Requests\Backend\Operations\Tours;

use App\Models\TourPrices;
use App\Models\Tours;
use App\Services\Tours\TourPriceOverlapValidator;
use App\Support\CanonicalDateInput;
use App\Support\CanonicalDecimalInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreTourPriceAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && $user->can('isAdmin')
            && ($user->can('posDev') || $user->can('posAuthor'));
    }

    public function rules(): array
    {
        return [
            'min_qty' => ['required', 'integer', 'min:1'],
            'max_qty' => ['required', 'integer', 'gte:min_qty'],
            'contract_rate_idr' => ['required', 'integer', 'min:1'],
            'markup_type' => ['required', Rule::in(TourPrices::MARKUP_TYPES)],
            'markup_amount' => ['required', 'regex:/^\d+(?:\.\d{1,2})?$/'],
            'valid_from' => ['required', 'date_format:Y-m-d'],
            'valid_until' => ['required', 'date_format:Y-m-d', 'after_or_equal:valid_from'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $markup = (string) $this->input('markup_amount');
            $markupType = $this->input('markup_type');

            if ($markupType === TourPrices::MARKUP_TYPE_IDR && ! preg_match('/^\d+$/', $markup)) {
                $validator->errors()->add('markup_amount', 'Markup IDR must be a whole rupiah amount.');
            }

            if ($markupType === TourPrices::MARKUP_TYPE_USD
                && preg_match('/\.(\d+)/', $markup, $matches)
                && strlen($matches[1]) > 2
            ) {
                $validator->errors()->add('markup_amount', 'Markup USD may have at most two decimal places.');
            }

            if ($markupType === TourPrices::MARKUP_TYPE_PERCENTAGE && (float) $markup > 100) {
                $validator->errors()->add('markup_amount', 'Percentage markup may not exceed 100%.');
            }

            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $tour = $this->route('tour');

            if (! $tour instanceof Tours) {
                return;
            }

            $conflicts = app(TourPriceOverlapValidator::class)->conflicts(
                (int) $tour->id,
                $this->integer('min_qty'),
                $this->integer('max_qty'),
                (string) $this->input('valid_from'),
                (string) $this->input('valid_until'),
                $this->overlapExceptPriceId(),
            );

            if ($conflicts) {
                $validator->errors()->add(
                    'min_qty',
                    'The pax tier overlaps another price during the selected validity period.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'contract_rate_idr.required' => 'Contract rate IDR is required.',
            'contract_rate_idr.min' => 'Contract rate IDR must be greater than zero.',
            'markup_type.required' => 'Markup type is required.',
            'markup_type.in' => 'Markup type must be Percentage, USD, or IDR.',
            'markup_amount.required' => 'Markup amount is required.',
            'markup_amount.regex' => 'Markup must be a non-negative number with at most two decimal places.',
            'valid_from.required' => 'Valid from is required.',
            'valid_until.required' => 'Valid until is required.',
            'valid_until.after_or_equal' => 'Valid until must be on or after valid from.',
            'min_qty.min' => 'Minimum pax must be at least 1.',
            'max_qty.gte' => 'Maximum pax must be greater than or equal to minimum pax.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $contractRate = $this->input('contract_rate_idr');
        $markup = $this->input('markup_amount');
        $markupType = $this->input('markup_type');
        $validFrom = CanonicalDateInput::normalize($this->input('valid_from'));
        $validUntil = CanonicalDateInput::normalize($this->input('valid_until'));

        if (is_string($contractRate)) {
            $contractRate = preg_replace('/[^\d-]/', '', trim($contractRate));
        }

        if (is_string($markup)) {
            $markup = trim($markup);
            if (str_contains($markup, ',') && ! str_contains($markup, '.')) {
                $markup = str_replace(',', '.', $markup);
            }
            $markup = CanonicalDecimalInput::normalize($markup);
        }

        if (! is_string($markupType) || $markupType === '') {
            $markupType = match (strtoupper((string) $this->input('markup_currency'))) {
                'USD' => TourPrices::MARKUP_TYPE_USD,
                'IDR' => TourPrices::MARKUP_TYPE_IDR,
                default => null,
            };
        }

        $this->merge([
            'contract_rate_idr' => $contractRate,
            'markup_type' => is_string($markupType) ? strtolower($markupType) : $markupType,
            'markup_amount' => $markup,
            'valid_from' => $validFrom,
            'valid_until' => $validUntil,
        ]);
    }

    protected function overlapExceptPriceId(): ?int
    {
        return null;
    }
}
