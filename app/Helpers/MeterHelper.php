<?php

namespace App\Helpers;

use App\Models\Billings;
use App\Models\MeterChange;
use Carbon\Carbon;

class MeterHelper
{
    /**
     * Calculate the cubic meters consumed between two readings,
     * accounting for any mid-cycle meter replacement.
     *
     * If a meter change occurred between the two reading dates:
     *   - "New Meter Consumption" = presentReading - new_meter_start_reading
     *   - "Old Meter Consumption" = old_meter_final_reading - previousReading
     *     (falls back to historical average if old meter was broken/unreadable)
     *
     * @param  string  $memberID
     * @param  object  $previousReading  Readings model instance
     * @param  object  $presentReading   Readings model instance
     * @return float
     */
    public static function calculateCubicMeterUsed($memberID, $previousReading, $presentReading): float
    {
        $meterChange = MeterChange::where('member_id', $memberID)
            ->whereDate('change_date', '>', Carbon::parse($previousReading->reading_date))
            ->whereDate('change_date', '<=', Carbon::parse($presentReading->reading_date))
            ->orderByDesc('change_date')
            ->first();

        if ($meterChange) {
            // Consumption on NEW meter since it was installed
            $newMeterConsumption = $presentReading->reading - $meterChange->new_meter_start_reading;
            if ($newMeterConsumption < 0) {
                $newMeterConsumption = 0; // Safeguard against bad data
            }

            if (is_null($meterChange->old_meter_final_reading)) {
                // OLD METER BROKEN – fall back to historical average consumption
                $oldMeterConsumption = (float) (Billings::where('member_id', $memberID)
                    ->where('billing_month', '<', Carbon::parse($presentReading->reading_date)->subDays(15)->format('Y-m-01'))
                    ->avg('cubic_meter_used') ?? 0);
            } else {
                // Normal: consumption on old meter up to the point it was replaced
                $oldMeterConsumption = $meterChange->old_meter_final_reading - $previousReading->reading;
                if ($oldMeterConsumption < 0) {
                    $oldMeterConsumption = 0; // Safeguard
                }
            }

            return round((float) $oldMeterConsumption + (float) $newMeterConsumption, 2);
        }

        // No meter change – standard calculation
        return (float) ($presentReading->reading - $previousReading->reading);
    }
}
