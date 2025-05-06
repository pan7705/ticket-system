<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketCalled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;
    public $counter;
    public $nowServing;

    public function __construct($ticket, $counter, $nowServing)
    {
        $this->ticket = $ticket;
        $this->counter = $counter;
        $this->nowServing = $nowServing;
    }

    public function broadcastOn()
    {
        return new Channel('tickets');
    }
}
