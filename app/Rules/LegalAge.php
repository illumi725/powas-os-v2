<?php

namespace App\Rules;

use Closure;
use DateTime;
use Illuminate\Contracts\Validation\ValidationRule;

class LegalAge implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $today = new DateTime();
        $birthdate = new DateTime($value);
        $age = $today->diff($birthdate)->y;

        if ($age <= 18 || $age >= 90) {
            if ($age < 18) {
                $fail('You are too young to apply for POWAS. (' . $age . ' years old)');
            } elseif ($age > 90) {
                $fail('You are too old to apply for POWAS.  (' . $age . ' years old)');
            }
        }
    }
}
