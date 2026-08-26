<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransportBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('isAdmin')
            && ($this->user()?->can('posDev') || $this->user()?->can('posAuthor'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'brand' => 'required|string|max:255|unique:transport_brands,brand,' . $this->route('transportBrand')?->id,
        ];
    }
}
