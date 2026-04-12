<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DamayanCollections extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'collection_id';
    public $incrementing = false;

    protected $fillable = [
        'collection_id',
        'powas_id',
        'disbursement_id',
        'amount',
        'recorded_by',
    ];

    public function colpowas(): BelongsTo
    {
        return $this->belongsTo(Powas::class, 'powas_id', 'powas_id');
    }

    public function coluser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by', 'user_id');
    }

    public function coldisbursement(): BelongsTo
    {
        return $this->belongsTo(DamayanBeneficiaries::class, 'disbursement_id', 'disbursement_id');
    }
}
