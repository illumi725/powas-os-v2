<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TransactionImport implements WithHeadingRow
{
    protected $powasID;

    public function __construct($powasID)
    {
        $this->powasID = $powasID;
    }
}
