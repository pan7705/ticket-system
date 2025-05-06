<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Counter;
use App\Events\TicketCreated;

class CustomerController extends Controller
{
    public function index()
    {
        $counters = Counter::all();
        $lastTicket = Ticket::orderBy('id', 'desc')->first();
        $nowServing = Ticket::where('status', 'serving')->first();

        $lastTicketNumber = $lastTicket ? $lastTicket->ticket_number : 0;
        $nowServingNumber = $nowServing ? $nowServing->ticket_number : null;

        return view('customer.index', compact('counters', 'lastTicketNumber', 'nowServingNumber'));
    }

    public function takeNumber()
    {
        $lastTicket = Ticket::orderBy('id', 'desc')->first();
        $newTicketNumber = $lastTicket ? $lastTicket->ticket_number + 1 : 1;

        $ticket = Ticket::create([
            'ticket_number' => $newTicketNumber,
            'status' => 'waiting'
        ]);

        event(new TicketCreated($ticket, $newTicketNumber));

        return response()->json([
            'ticket_number' => $newTicketNumber
        ]);
    }
}
