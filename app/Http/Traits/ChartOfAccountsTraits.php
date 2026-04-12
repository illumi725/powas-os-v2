<?php

namespace App\Http\Traits;

use App\Models\ChartOfAccounts;

trait ChartOfAccountsTraits
{
    public static function isAccountNumberExists($accountnumber)
    {
        return ChartOfAccounts::where('account_number', $accountnumber)->exists();
    }

    public static function isAccountNameExists($accountname)
    {
        return ChartOfAccounts::where('account_name', $accountname)->exists();
    }
}
