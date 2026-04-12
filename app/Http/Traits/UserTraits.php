<?php

namespace App\Http\Traits;

trait UserTraits
{
    public function isActive()
    {
        return $this->account_status == 'ACTIVE';
    }

    public function getAccountStatus()
    {
        return $this->account_status;
    }
}
