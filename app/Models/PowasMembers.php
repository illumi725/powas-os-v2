<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PowasMembers extends Model
{
    use HasFactory;

    protected $primaryKey = 'member_id';
    public $incrementing = false;

    protected $fillable = [
        'member_id',
        'application_id',
        'meter_number',
        'membership_date',
        'firstfifty',
        'land_owner',
        'member_status',
    ];

    public function applicationinfo(): HasOne
    {
        return $this->hasOne(PowasApplications::class, 'application_id', 'application_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transactions::class, 'member_id', 'member_id');
    }

    public function meterreadings(): HasMany
    {
        return $this->hasMany(Readings::class, 'member_id', 'member_id');
    }

    public function memberbilling(): HasMany
    {
        return $this->hasMany(Billings::class, 'member_id', 'member_id');
    }

    public function memberbillspayments(): HasMany
    {
        return $this->hasMany(BillsPayments::class, 'member_id', 'member_id');
    }

    public function membermicrosavings(): HasMany
    {
        return $this->hasMany(MicroSavings::class, 'member_id', 'member_id');
    }

    public function memberreconnectionfee(): HasMany
    {
        return $this->hasMany(ReconnectionFees::class, 'member_id', 'member_id');
    }

    public function memberdamayanbeneficiaries(): HasMany
    {
        return $this->hasMany(DamayanBeneficiaries::class, 'member_id', 'member_id');
    }

    public function memberdamayandisbursement(): HasOne
    {
        return $this->hasOne(DamayanDisbursements::class, 'member_id', 'member_id');
    }

    public function meterchanges(): HasMany
    {
        return $this->hasMany(MeterChange::class, 'member_id', 'member_id');
    }
}
