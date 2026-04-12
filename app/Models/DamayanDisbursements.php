<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DamayanDisbursements extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'disbursement_id';
    public $incrementing = false;

    protected $fillable = [
        'disbursement_id',
        'powas_id',
        'beneficiary_id',
        'member_id',
        'grantee',
        'amount',
        'disbursement_status',
        'disbursement_date',
        'death_cert_image',
        'recorded_by',
    ];

    public function memberdamayandisbursement(): BelongsTo
    {
        return $this->belongsTo(PowasMembers::class, 'member_id', 'member_id');
    }

    public function damayandisbursement(): BelongsTo
    {
        return $this->belongsTo(DamayanBeneficiaries::class, 'beneficiary_id', 'beneficiary_id');
    }

    public function powasdamayandisbursement(): BelongsTo
    {
        return $this->belongsTo(Powas::class, 'powas_id', 'powas_id');
    }

    public function disuser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by', 'user_id');
    }

    public function coldisbursement(): HasMany
    {
        return $this->hasMany(DamayanCollections::class, 'disbursement_id', 'disbursement_id');
    }
}
