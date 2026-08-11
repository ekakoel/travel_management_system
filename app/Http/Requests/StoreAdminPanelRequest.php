<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdminPanelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()?->can('posDev') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'nicname' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('services', 'nicname'),
            ],
            'icon' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9 _-]+$/'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'nicname' => strtolower(trim((string) $this->input('nicname'))),
            'icon' => $this->normalizeIconClass($this->input('icon')),
        ]);
    }

    private function normalizeIconClass(mixed $icon): string
    {
        $value = trim((string) $icon);

        if (preg_match('/class=["\']([^"\']+)["\']/i', $value, $matches)) {
            return trim($matches[1]);
        }

        return trim(strip_tags($value));
    }
}
