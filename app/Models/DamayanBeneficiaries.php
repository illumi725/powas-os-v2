<?php

namespace App\Models;

use App\Http\Traits\DamayanBeneficiariesTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class DamayanBeneficiaries extends Model
{
    use DamayanBeneficiariesTraits;
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'beneficiary_id';
    public $incrementing = false;

    protected $fillable = [
        'beneficiary_id',
        'powas_id',
        'member_id',
        'lastname',
        'firstname',
        'middlename',
        'birthday',
        'recipient',
    ];

    public function powasdamayanbeneficiaries(): BelongsTo
    {
        return $this->belongsTo(Vouchers::class, 'powas_id', 'powas_id');
    }

    public function memberdamayanbeneficiaries(): BelongsTo
    {
        return $this->belongsTo(PowasMembers::class, 'member_id', 'powas_id');
    }

    public function damayandisbursement(): HasOne
    {
        return $this->hasOne(DamayanDisbursements::class, 'beneficiary_id', 'beneficiary_id');
    }
}
