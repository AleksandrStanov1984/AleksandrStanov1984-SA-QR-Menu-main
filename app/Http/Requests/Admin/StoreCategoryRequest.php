<?php

namespace App\Http\Requests\Admin;

use App\Rules\FirstLetterUppercase;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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

            $titles = $this->input('title', []);

            $hasAny = collect($titles)
                ->filter(fn($t) => !empty(trim($t)))
                ->isNotEmpty();

            if (!$hasAny) {
                $v->errors()->add(
                    'title',
                    __('admin.validation.title_required')
                );
            }
        });
    }
}
