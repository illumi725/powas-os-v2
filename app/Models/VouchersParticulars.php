<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VouchersParticulars extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'voucher_id',
        'particulars',
        'description',
    ];

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Vouchers::class, 'voucher_id', 'voucher_id');
    }
}
