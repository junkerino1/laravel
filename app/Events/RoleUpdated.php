<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoleUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $user;
    public $roleName;

    public function __construct($user, $roleName)
    {
        $this->user = $user;
        $this->roleName = $roleName;
    }

    public function broadcastOn()
    {
        return new Channel('role-update');
    }

    public function broadcastAs(){
        return 'RoleUpdated';
    }

    public function broadcastWith(){
        return ['role' => $this->roleName];
    }
}
