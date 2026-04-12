<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneNumberFormat2 implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $pattern = '/^(09\d{9}|\+639\d{9})$/';
        if (!preg_match($pattern, $value)) {
            $fail('The phone number must have the format 09XXXXXXXXX or +639XXXXXXXX.');
        }
    }
}
