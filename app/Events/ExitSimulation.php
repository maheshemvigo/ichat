<?php

namespace App\Events;

use App\User;
use App\Message;
use App\Token;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ExitSimulation implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $simulation_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($simulation_id)
    {
        $this->simulation_id = $simulation_id;
        // print_r($this->simulation_id);exit();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */

    public function broadcastWith()
    {
        $userid = Token::select('user_id')->where('simulation_id', $this->simulation_id)->get();

        return [
            'users' => $userid,
            'simulation_id' => $this->simulation_id
        ];
    }

    public function broadcastOn()
    {
        return new Channel('exit');
    }
}
