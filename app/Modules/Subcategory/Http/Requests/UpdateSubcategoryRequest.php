<?php

namespace App\Modules\Subcategory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubcategoryRequest extends FormRequest
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
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => [
                'required',
                'string',
                'max:100',
                // Unique inside the chosen category, ignoring this subcategory itself.
                Rule::unique('subcategories', 'name')
                    ->where('category_id', $this->integer('category_id'))
                    ->ignore($this->route('subcategory')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'This category already has a subcategory with that name.',
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'category',
        ];
    }
}
