<?php

namespace App\Models;

use App\Http\Traits\POWASTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Powas extends Model
{
    use HasFactory, SoftDeletes;
    use POWASTraits;

    protected $primaryKey = 'powas_id';
    public $incrementing = false;

    protected $fillable = [
        'powas_id',
        'region',
        'province',
        'municipality',
        'barangay',
        'zone',
        'phase',
        'inauguration_date',
        'status',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'powas_id', 'powas_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(PowasApplications::class, 'powas_id', 'powas_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transactions::class, 'powas_id', 'powas_id');
    }

    public function settings(): HasOne
    {
        return $this->hasOne(PowasSettings::class, 'powas_id', 'powas_id');
    }

    public function issuedreceipt(): HasMany
    {
        return $this->hasMany(IssuedReceipts::class, 'powas_id', 'powas_id');
    }

    public function powaslogs(): HasMany
    {
        return $this->hasMany(PowasOsLogs::class, 'powas_id', 'powas_id');
    }

    public function powasreading(): HasMany
    {
        return $this->hasMany(Readings::class, 'powas_id', 'powas_id');
    }

    public function powasbilling(): HasMany
    {
        return $this->hasMany(Billings::class, 'powas_id', 'powas_id');
    }

    public function powasbillspayment(): HasMany
    {
        return $this->hasMany(BillsPayments::class, 'powas_id', 'powas_id');
    }

    public function powasmicrosavings(): HasMany
    {
        return $this->hasMany(MicroSavings::class, 'powas_id', 'powas_id');
    }

    public function powasreconnectionfee(): HasMany
    {
        return $this->hasMany(ReconnectionFees::class, 'powas_id', 'powas_id');
    }

    public function powasvoucher(): HasMany
    {
        return $this->hasMany(Vouchers::class, 'powas_id', 'powas_id');
    }

    public function powasdamayanbeneficiaries(): HasMany
    {
        return $this->hasMany(DamayanBeneficiaries::class, 'powas_id', 'powas_id');
    }

    public function powasdamayandisbursements(): HasMany
    {
        return $this->hasMany(DamayanDisbursements::class, 'powas_id', 'powas_id');
    }

    public function powasdamayancollection(): HasMany
    {
        return $this->hasMany(DamayanCollections::class, 'powas_id', 'powas_id');
    }
}
