<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FirstLetterUppercase implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || $value === '') return;

        $first = mb_substr($value, 0, 1);
        if ($first !== mb_strtoupper($first)) {
            $fail(__('admin.validation.first_letter_uppercase'));
        }
    }
}
