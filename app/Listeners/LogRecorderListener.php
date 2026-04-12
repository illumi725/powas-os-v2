<?php

namespace App\Listeners;

use App\Events\ActionLogger;
use App\Factory\CustomNumberFactory;
use App\Models\PowasOsLogs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogRecorderListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ActionLogger $event): void
    {
        $log_id = CustomNumberFactory::getRandomID();
        $action_type = $event->action_type;
        $log_message = $event->log_message;
        $user_id = $event->user_id;
        $powas_id = $event->powas_id;
        $log_blade = $event->log_blade;

        PowasOsLogs::create([
            'log_id' => $log_id,
            'action_type' => $action_type,
            'log_message' => $log_message,
            'user_id' => $user_id,
            'powas_id' => $powas_id,
            'log_blade' => $log_blade,
        ]);
    }
}
