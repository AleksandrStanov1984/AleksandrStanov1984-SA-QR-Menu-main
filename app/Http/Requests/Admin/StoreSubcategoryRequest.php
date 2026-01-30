<?php

namespace App\Http\Requests\Admin;

use App\Models\Section;
use App\Rules\FirstLetterUppercase;
use Illuminate\Foundation\Http\FormRequest;

class StoreSubcategoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        $t = $this->input('title');
        if (is_string($t)) {
            $t = trim($t);
            $t = strip_tags($t);
            $t = preg_replace('/\s+/u', ' ', $t);
            $this->merge(['title' => $t]);
        }
    }

    public function rules(): array
    {
        return [
            'parent_id' => ['required', 'integer', 'exists:sections,id'],
            'title' => [
                'required',
                'string',
                'max:50',
                'regex:/^[\p{L}][\p{L}\s\-]*$/u',
                new FirstLetterUppercase(),
            ],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $parentId = (int) $this->input('parent_id');
            $parent = Section::query()->find($parentId);

            if (!$parent) return;

            // parent должен быть категорией (parent_id=null)
            if (!is_null($parent->parent_id)) {
                $v->errors()->add('parent_id', __('admin.validation.parent_must_be_category'));
            }
        });
    }
}
