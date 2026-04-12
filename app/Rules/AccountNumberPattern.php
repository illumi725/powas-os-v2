<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AccountNumberPattern implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $pattern = '/^[A-Z]{3}-[A-Z]{3}-[A-Z]{3}-\d{3}-\d{4}$/';
        if (!preg_match($pattern, $value)) {
            $fail('The account number must have the format XXX-XXX-XXX-000-0000.');
        }
    }
}
