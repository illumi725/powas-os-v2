<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReconnectionFees extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'reconnection_id';
    public $incrementing = false;

    protected $fillable = [
        'reconnection_id',
        'powas_id',
        'member_id',
        'recorded_by',
        'billing_id',
        'amount',
        'date_recorded',
    ];

    public function userreconnectionfee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by', 'user_id');
    }

    public function powasreconnectionfee(): BelongsTo
    {
        return $this->belongsTo(Powas::class, 'powas_id', 'powas_id');
    }

    public function memberreconnectionfee(): BelongsTo
    {
        return $this->belongsTo(PowasMembers::class, 'member_id', 'member_id');
    }

    public function billingreconnectionfee(): BelongsTo
    {
        return $this->belongsTo(Billings::class, 'billing_id', 'billing_id');
    }
}
