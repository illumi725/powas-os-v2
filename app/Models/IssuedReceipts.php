<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class IssuedReceipts extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'print_id';
    public $incrementing = false;

    protected $fillable = [
        'print_id',
        'receipt_number',
        'trxn_id',
        'powas_id',
        'is_printed',
        'print_count',
        'transaction_date',
    ];

    public function printaction(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function receiptprint(): BelongsTo
    {
        return $this->belongsTo(Transactions::class, 'trxn_id', 'trxn_id');
    }

    public function powasreceipt(): BelongsTo
    {
        return $this->belongsTo(Powas::class, 'powas_id', 'powas_id');
    }
}
