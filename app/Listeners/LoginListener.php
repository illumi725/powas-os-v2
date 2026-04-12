<?php

namespace App\Listeners;

use App\Events\LoginTracker;
use App\Factory\CustomNumberFactory;
use App\Models\LoginLogs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LoginListener
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
    public function handle(LoginTracker $event): void
    {
        $login_id = CustomNumberFactory::getRandomID();
        $login_result = $event->login_result;
        $user_id = $event->user_id;

        LoginLogs::create([
            'login_id' => $login_id,
            'login_result' => $login_result,
            'user_id' => $user_id,
        ]);
    }
}
