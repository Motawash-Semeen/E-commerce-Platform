<?php

namespace App\Modules\Category\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Create and update. Access is limited by 'role:admin' on the route group.
 */
class CategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:100',
                // On update, keeping the same name must not fail as "already taken".
                Rule::unique('categories', 'name')->ignore($this->route('category')),
            ],
        ];
    }
}
