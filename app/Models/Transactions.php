<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transactions extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'trxn_id';
    public $incrementing = false;

    protected $fillable = [
        'trxn_id',
        'account_number',
        'description',
        'journal_entry_number',
        'amount',
        'transaction_side',
        'received_from',
        'paid_to',
        'member_id',
        'powas_id',
        'recorded_by_id',
        'transaction_date',
    ];

    public function chartofaccounts(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccounts::class, 'account_number', 'account_number');
    }

    public function members(): BelongsTo
    {
        return $this->belongsTo(PowasMembers::class, 'member_id', 'member_id');
    }

    public function powas(): BelongsTo
    {
        return $this->belongsTo(Powas::class, 'powas_id', 'powas_id');
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_id', 'user_id');
    }

    public function printedreceipt(): HasMany
    {
        return $this->hasMany(IssuedReceipts::class, 'trxn_id', 'trxn_id');
    }

    public function transactionsvoucher(): HasMany
    {
        return $this->hasMany(Vouchers::class, 'trxn_id', 'trxn_id');
    }

    public function bankslip(): HasOne
    {
        return $this->hasOne(BankSlipPictures::class, 'trxn_id', 'trxn_id');
    }
}
