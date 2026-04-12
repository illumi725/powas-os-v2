<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillsPayments extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'payment_id';
    public $incrementing = false;

    protected $fillable = [
        'payment_id',
        'powas_id',
        'member_id',
        'recorded_by',
        'billing_id',
        'amount_paid',
        'date_paid',
    ];

    public function userbillspayments(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by', 'user_id');
    }

    public function powasbillspayments(): BelongsTo
    {
        return $this->belongsTo(Powas::class, 'powas_id', 'powas_id');
    }

    public function memberbillspayments(): BelongsTo
    {
        return $this->belongsTo(PowasMembers::class, 'member_id', 'member_id');
    }

    public function billingbillspayments(): BelongsTo
    {
        return $this->belongsTo(Billings::class, 'billing_id', 'billing_id');
    }
}
