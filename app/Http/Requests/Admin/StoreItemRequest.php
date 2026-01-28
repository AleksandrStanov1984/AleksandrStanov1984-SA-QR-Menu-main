<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'translations' => ['required', 'array', 'min:1'],

            'translations.*.title' => ['required', 'string', 'max:50', 'regex:/^[^<>]*$/u'],
            'translations.*.description' => ['nullable', 'string', 'max:250', 'regex:/^[^<>]*$/u'],
            'translations.*.details' => ['nullable', 'string', 'max:500', 'regex:/^[^<>]*$/u'],

            'price' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'currency' => ['nullable', 'string', 'max:10'],

            'is_active' => ['nullable', 'boolean'],

            // meta flags
            'is_new' => ['nullable', 'boolean'],
            'dish_of_day' => ['nullable', 'boolean'],
            'show_image' => ['nullable', 'boolean'],
            'spicy' => ['nullable', 'integer', 'min:0', 'max:5'],

            // style (word-like)
            'style' => ['nullable', 'array'],
            'style.title' => ['nullable', 'array'],
            'style.desc' => ['nullable', 'array'],
            'style.details' => ['nullable', 'array'],

            'style.title.font' => ['nullable', 'string', 'max:50', 'regex:/^[^<>]*$/u'],
            'style.title.color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/'],
            'style.title.size' => ['nullable', 'integer', 'min:8', 'max:72'],

            'style.desc.font' => ['nullable', 'string', 'max:50', 'regex:/^[^<>]*$/u'],
            'style.desc.color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/'],
            'style.desc.size' => ['nullable', 'integer', 'min:8', 'max:72'],

            'style.details.font' => ['nullable', 'string', 'max:50', 'regex:/^[^<>]*$/u'],
            'style.details.color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/'],
            'style.details.size' => ['nullable', 'integer', 'min:8', 'max:72'],

            // image security
            'image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}
