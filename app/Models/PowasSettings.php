<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PowasSettings extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'powas_id',
        'water_rate',
        'first_50_fee',
        'application_fee',
        'minimum_payment',
        'members_micro_savings',
        'due_date_day',
        'reading_day',
        'collection_day',
        'days_before_disconnection',
        'penalty_per_day',
        'reconnection_fee',
        'land_owners_id',
        'bill_paper_size',
        'receipt_paper_size',
        'atp_number',
        'atp_date_issued',
        'atp_valid_until',
        'printer_name',
        'printer_address',
        'printer_tin',
        'printer_accreditation_no',
        'printer_accreditation_date',
        'serial_number_start',
        'serial_number_end',
        'current_serial_number',
    ];

    public function powassettings(): BelongsTo
    {
        return $this->belongsTo(Powas::class, 'powas_id', 'powas_id');
    }
}
