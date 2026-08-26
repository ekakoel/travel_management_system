<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateExtraBedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::any(['posDev', 'posAuthor']);
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', Rule::in(['Adult', 'Children', 'Guest'])],
            'min_age' => ['nullable', 'integer', 'min:0', 'max:120'],
            'max_age' => ['nullable', 'integer', 'min:0', 'max:120'],
            'description' => ['nullable', 'string'],
            'contract_rate' => ['required', 'numeric', 'min:0'],
            'markup' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $minAge = $this->input('min_age');
            $maxAge = $this->input('max_age');

            if ($minAge !== null && $minAge !== '' && $maxAge !== null && $maxAge !== '' && (int) $maxAge < (int) $minAge) {
                $validator->errors()->add('max_age', 'The max age must be greater than or equal to the min age.');
            }
        });
    }
}
