<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Readings extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'reading_id';
    public $incrementing = false;

    protected $fillable = [
        'reading_id',
        'member_id',
        'powas_id',
        'recorded_by',
        'reading',
        'reading_date',
        'reading_count',
        'meter_number',
    ];

    public function memberreading(): BelongsTo
    {
        return $this->belongsTo(PowasMembers::class, 'member_id', 'member_id');
    }

    public function powasreading(): BelongsTo
    {
        return $this->belongsTo(Powas::class, 'powas_id', 'powas_id');
    }

    public function userreading(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by', 'user_id');
    }
}
