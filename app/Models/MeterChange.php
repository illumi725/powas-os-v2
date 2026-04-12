<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeterChange extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'old_meter_number',
        'new_meter_number',
        'old_meter_final_reading',
        'new_meter_start_reading',
        'change_date',
        'reason',
        'recorded_by',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(PowasMembers::class, 'member_id', 'member_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by', 'user_id');
    }
}
