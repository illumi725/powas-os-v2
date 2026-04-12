<?php

namespace App\Models;

use App\Http\Traits\POWASApplicationTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PowasApplications extends Model
{
    use HasFactory, POWASApplicationTraits, SoftDeletes;

    protected $primaryKey = 'application_id';
    public $incrementing = false;

    protected $fillable = [
        'application_id',
        'powas_id',
        'lastname',
        'firstname',
        'middlename',
        'birthday',
        'birthplace',
        'gender',
        'contact_number',
        'civil_status',
        'address1',
        'barangay',
        'municipality',
        'province',
        'region',
        'present_address',
        'family_members',
        'application_status',
        'by_user_id',
        'application_date',
        'add_mode',
        'id_path',
        'reject_reason',
    ];

    public function powas(): BelongsTo
    {
        return $this->belongsTo(Powas::class, 'powas_id', 'powas_id');
    }

    public function manage_application(): BelongsTo
    {
        return $this->belongsTo(User::class, 'by_user_id', 'user_id');
    }

    public function members(): BelongsTo
    {
        return $this->belongsTo(PowasMembers::class, 'application_id', 'application_id');
    }
}
