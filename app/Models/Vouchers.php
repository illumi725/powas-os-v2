<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vouchers extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'voucher_id';
    public $incrementing = false;

    protected $fillable = [
        'voucher_id',
        'voucher_number',
        'powas_id',
        'trxn_id',
        'recorded_by',
        'amount',
        'received_by',
        'prepared_by',
        'checked_by',
        'approved_by',
        'voucher_date',
    ];

    public function uservoucher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by', 'user_id');
    }

    public function powasvoucher(): BelongsTo
    {
        return $this->belongsTo(Powas::class, 'powas_id', 'powas_id');
    }

    public function transactionsvoucher(): BelongsTo
    {
        return $this->belongsTo(Transactions::class, 'trxn_id', 'trxn_id');
    }

    public function voucherparticulars(): HasMany
    {
        return $this->hasMany(VouchersParticulars::class, 'voucher_id', 'voucher_id');
    }

    public function voucherexpensereceipts(): HasMany
    {
        return $this->hasMany(VoucherExpenseReceipts::class, 'voucher_id', 'voucher_id');
    }
}
