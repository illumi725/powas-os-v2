<?php

namespace App\Http\Traits;

use App\Models\Powas;

trait POWASTraits
{
    public static function isExists($region, $province, $municipality, $barangay, $phase)
    {
        return Powas::where('region', $region)
            ->where('province', $province)
            ->where('municipality', $municipality)
            ->where('barangay', $barangay)
            ->where('phase', $phase)
            ->exists();
    }
}
