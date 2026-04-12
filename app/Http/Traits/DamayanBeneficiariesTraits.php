<?php

namespace App\Http\Traits;

use App\Models\DamayanBeneficiaries;

trait DamayanBeneficiariesTraits
{
    public static function isExisting(array $inputs)
    {
        return DamayanBeneficiaries::where($inputs)->exists();
    }

    public static function getExistingApplication(array $inputs)
    {
        return DamayanBeneficiaries::where($inputs)->first();
    }
}
