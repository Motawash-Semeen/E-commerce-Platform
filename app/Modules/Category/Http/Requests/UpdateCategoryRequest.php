<?php

namespace App\Modules\Category\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Access is already limited by the 'auth' middleware on the admin group.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                // Keeping the same name on save must not fail as "already taken".
                Rule::unique('categories', 'name')->ignore($this->route('category')),
            ],
        ];
    }
}
