<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;
    public $lastTicketNumber;

    public function __construct($ticket, $lastTicketNumber)
    {
        $this->ticket = $ticket;
        $this->lastTicketNumber = $lastTicketNumber;
    }

    public function broadcastOn()
    {
        return new Channel('tickets');
    }
}
