<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityTypeRequest extends FormRequest
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
            'type' => 'required|string|max:255|unique:activity_types,type',
        ];
    }
}
