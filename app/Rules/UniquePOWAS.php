<?php

namespace App\Rules;

use App\Models\Powas;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniquePOWAS implements ValidationRule
{
    protected $region;
    protected $province;
    protected $municipality;
    protected $barangay;
    protected $phase;

    /**
     * @param string $region
     * @param string $province
     * @param string $municipality
     * @param string $barangay
     * @param string $phase
     */

    public function __construct($region, $province, $municipality, $barangay, $phase)
    {
        $this->region = $region;
        $this->province = $province;
        $this->municipality = $municipality;
        $this->barangay = $barangay;
        $this->phase = $phase;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (Powas::isExists(
            region: $this->region,
            province: $this->province,
            municipality: $this->municipality,
            barangay: $this->barangay,
            phase: $this->phase
        )) {
            $fail($this->barangay . ' POWAS ' . $this->phase . ' already exists!');
        }
    }
}
