<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PowasOsLogs extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'log_id';
    public $incrementing = false;

    protected $fillable = [
        'log_id',
        'action_type',
        'log_message',
        'user_id',
        'powas_id',
        'log_blade',
    ];

    public function actionlog(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function powaslog(): BelongsTo
    {
        return $this->belongsTo(User::class, 'powas_id', 'powas_id');
    }
}
