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

            'position_mode' => ['nullable', 'in:start,end,before,after'],
            'target_id'     => ['nullable', 'integer', 'exists:sections,id'],

            'title.*' => [
                'nullable',
                'string',
                'max:50',
                new FirstLetterUppercase(),
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {

            // =========================
            // VALIDATE PARENT
            // =========================
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

            // =========================
            // TITLE REQUIRED
            // =========================
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

            // =========================
            // TITLE FORMAT
            // =========================
            foreach ($titles as $t) {
                $t = trim((string) $t);

                if ($t !== '' && !preg_match('/^[\p{L}\d]/u', $t)) {
                    $v->errors()->add(
                        'title',
                        __('admin.validation.title_invalid')
                    );
                    break;
                }
            }

            // =========================
            // POSITION VALIDATION
            // =========================
            $mode   = $this->input('position_mode');
            $target = $this->input('target_id');

            if (in_array($mode, ['before', 'after'], true) && !$target) {
                $v->errors()->add(
                    'target_id',
                    __('admin.position.target_required')
                );
            }

        });
    }
}
