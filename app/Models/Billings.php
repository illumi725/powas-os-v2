<?php

namespace App\Models;

use App\Http\Traits\QRCodeGenerationTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Billings extends Model
{
    use HasFactory, SoftDeletes;
    use QRCodeGenerationTraits;

    protected $primaryKey = 'billing_id';
    public $incrementing = false;

    protected $fillable = [
        'billing_id',
        'powas_id',
        'member_id',
        'recorded_by',
        'previous_reading_id',
        'present_reading_id',
        'cubic_meter_used',
        'billing_amount',
        'discount_amount',
        'penalty',
        'billing_month',
        'due_date',
        'cut_off_start',
        'cut_off_end',
        'bill_number',
        'print_count',
        'bill_status',
    ];

    public function userbilling(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by', 'user_id');
    }

    public function powasbilling(): BelongsTo
    {
        return $this->belongsTo(Powas::class, 'powas_id', 'powas_id');
    }

    public function memberbilling(): BelongsTo
    {
        return $this->belongsTo(PowasMembers::class, 'member_id', 'member_id');
    }

    public function billingbillspayment(): HasMany
    {
        return $this->hasMany(BillsPayments::class, 'billing_id', 'billing_id');
    }

    public function billingmicrosavings(): HasMany
    {
        return $this->hasMany(MicroSavings::class, 'billing_id', 'billing_id');
    }

    public function billingreconnectionfee(): HasMany
    {
        return $this->hasMany(ReconnectionFees::class, 'billing_id', 'billing_id');
    }
}
