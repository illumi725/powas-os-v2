<?php

namespace App\Http\Traits;

use App\Models\PowasApplications;

trait POWASApplicationTraits
{
    public static function isExisting(array $inputs)
    {
        return PowasApplications::where($inputs)->exists();
    }

    public static function getExistingApplication(array $inputs)
    {
        return PowasApplications::where($inputs)->first();
    }
}
