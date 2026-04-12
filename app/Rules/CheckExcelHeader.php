<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Maatwebsite\Excel\Facades\Excel;

class CheckExcelHeader implements ValidationRule
{
    protected $headers;

    /**
     * @param array $headers
     */

    public function __construct(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $importer = new class implements \Maatwebsite\Excel\Concerns\ToArray {
            public function array(array $array): void
            {
            }
        };

        $headersInExcel = Excel::toArray($importer, $value)[0][0];

        $checker = true;

        foreach ($this->headers as $header) {
            if (!in_array($header, $headersInExcel)) {
                $checker = false;
            }
        }

        if ($checker === false) {
            $fail('The excel file is invalid!');
        }
    }
}
