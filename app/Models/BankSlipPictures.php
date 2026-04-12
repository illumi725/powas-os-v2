<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankSlipPictures extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'trxn_id',
        'transaction_type',
        'bank_slip_image',
    ];

    public function bankslip(): BelongsTo
    {
        return $this->belongsTo(Transactions::class, 'trxn_id', 'trxn_id');
    }
}
