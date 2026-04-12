<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActionLogger
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $action_type;
    public $log_message;
    public $user_id;
    public $powas_id;
    public $log_blade;

    /**
     * Create a new event instance.
     */
    public function __construct($action_type, $log_message, $user_id, $log_blade, $powas_id = null)
    {
        $this->action_type = $action_type;
        $this->log_message = $log_message;
        $this->user_id = $user_id;
        $this->log_blade = $log_blade;
        $this->powas_id = $powas_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
