<?php

namespace App\Http\Requests\Admin;

use App\Rules\FirstLetterUppercase;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
            'title' => [
                'required',
                'string',
                'max:50',
                // без цифр и спецсимволов; разрешаем буквы + пробел + дефис
                'regex:/^[\p{L}][\p{L}\s\-]*$/u',
                new FirstLetterUppercase(),
            ],
        ];
    }
}
