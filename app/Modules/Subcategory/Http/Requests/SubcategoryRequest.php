<?php

namespace App\Modules\Subcategory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Create and update. Access is limited by 'role:admin' on the route group.
 */
class SubcategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => [
                'required', 'string', 'max:100',
                // Unique inside the chosen category (matches the DB index), ignoring itself on update.
                Rule::unique('subcategories', 'name')
                    ->where('category_id', $this->integer('category_id'))
                    ->ignore($this->route('subcategory')),
            ],
        ];
    }

    public function messages(): array
    {
        return ['name.unique' => 'This category already has a subcategory with that name.'];
    }

    public function attributes(): array
    {
        return ['category_id' => 'category'];
    }
}
