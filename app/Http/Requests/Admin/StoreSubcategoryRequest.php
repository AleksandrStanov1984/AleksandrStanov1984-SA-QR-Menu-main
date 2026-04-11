<?php

namespace App\Http\Requests\Admin;

use App\Models\Section;
use App\Rules\FirstLetterUppercase;
use Illuminate\Foundation\Http\FormRequest;

class StoreSubcategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $titles = $this->input('title');

        if (is_array($titles)) {
            $clean = [];

            foreach ($titles as $loc => $t) {
                $t = trim((string) $t);
                $t = strip_tags($t);
                $t = preg_replace('/\s+/u', ' ', $t);

                $clean[$loc] = $t;
            }

            $this->merge(['title' => $clean]);
        }
    }

    public function rules(): array
    {
        return [
            'parent_id' => ['required', 'integer', 'exists:sections,id'],

            'title' => ['required', 'array'],

            'title.*' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[\p{L}][\p{L}\s\-]*$/u',
                new FirstLetterUppercase(),
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {

            $parentId = (int) $this->input('parent_id');
            $parent = Section::query()->find($parentId);

            if (!$parent) {
                return;
            }

            if (!is_null($parent->parent_id)) {
                $v->errors()->add(
                    'parent_id',
                    __('admin.validation.parent_must_be_category')
                );
            }

            $titles = $this->input('title', []);

            $hasAny = collect($titles)->filter(fn($t) => !empty(trim($t)))->isNotEmpty();

            if (!$hasAny) {
                $v->errors()->add(
                    'title',
                    __('admin.validation.title_required')
                );
            }
        });
    }
}
