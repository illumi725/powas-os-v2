<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MicroSavings extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'savings_id';
    public $incrementing = false;

    protected $fillable = [
        'savings_id',
        'powas_id',
        'member_id',
        'recorded_by',
        'billing_id',
        'deposit',
        'withdrawal',
        'balance',
        'date_recorded',
    ];

    public function usermicrosavings(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by', 'user_id');
    }

    public function powasmicrosavings(): BelongsTo
    {
        return $this->belongsTo(Powas::class, 'powas_id', 'powas_id');
    }

    public function membermicrosavings(): BelongsTo
    {
        return $this->belongsTo(PowasMembers::class, 'member_id', 'member_id');
    }

    public function billingmicrosavings(): BelongsTo
    {
        return $this->belongsTo(Billings::class, 'billing_id', 'billing_id');
    }
}
