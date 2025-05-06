<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Counter;
use App\Models\Ticket;

class TicketSystemController extends Controller
{
    public function index()
    {
        // Create counters if they don't exist
        if (Counter::count() == 0) {
            for ($i = 1; $i <= 4; $i++) {
                Counter::create([
                    'is_online' => true,
                    'is_serving' => false
                ]);
            }
        }

        $counters = Counter::all();
        $lastTicket = Ticket::orderBy('id', 'desc')->first();
        $nowServing = Ticket::where('status', 'serving')->first();

        $lastTicketNumber = $lastTicket ? $lastTicket->ticket_number : 0;
        $nowServingNumber = $nowServing ? $nowServing->ticket_number : 'None';

        return view('ticket-system', compact('counters', 'lastTicketNumber', 'nowServingNumber'));
    }

    public function takeNumber()
    {
        $lastTicket = Ticket::orderBy('id', 'desc')->first();
        $newTicketNumber = $lastTicket ? $lastTicket->ticket_number + 1 : 1;

        $ticket = Ticket::create([
            'ticket_number' => $newTicketNumber,
            'status' => 'waiting'
        ]);

        return response()->json([
            'success' => true,
            'ticket_number' => $newTicketNumber,
            'last_number' => $newTicketNumber
        ]);
    }

    public function toggleStatus($id)
    {
        $counter = Counter::findOrFail($id);
        $counter->is_online = !$counter->is_online;

        if (!$counter->is_online) {
            // If going offline, complete any current ticket
            if ($counter->is_serving && $counter->current_ticket_number) {
                $ticket = Ticket::where('counter_id', $counter->id)
                    ->where('status', 'serving')
                    ->first();

                if ($ticket) {
                    $ticket->status = 'completed';
                    $ticket->save();
                }
            }

            $counter->is_serving = false;
            $counter->current_ticket_number = null;
        }

        $counter->save();

        return response()->json([
            'success' => true,
            'counter' => $counter
        ]);
    }

    public function completeCurrent($id)
    {
        $counter = Counter::findOrFail($id);

        if (!$counter->is_online || !$counter->is_serving) {
            return response()->json([
                'success' => false,
                'message' => 'Counter is not serving any ticket'
            ]);
        }

        $ticket = Ticket::where('counter_id', $counter->id)
            ->where('status', 'serving')
            ->first();

        if ($ticket) {
            $ticket->status = 'completed';
            $ticket->save();

            $counter->is_serving = false;
            $counter->current_ticket_number = null;
            $counter->save();

            // Get the currently serving ticket
            $nowServing = Ticket::where('status', 'serving')->first();
            $nowServingNumber = $nowServing ? $nowServing->ticket_number : 'None';

            return response()->json([
                'success' => true,
                'counter' => $counter,
                'now_serving' => $nowServingNumber
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No ticket found'
        ]);
    }

    public function callNext($id)
    {
        $counter = Counter::findOrFail($id);

        if (!$counter->is_online) {
            return response()->json([
                'success' => false,
                'message' => 'Counter is offline'
            ]);
        }

        if ($counter->is_serving) {
            return response()->json([
                'success' => false,
                'message' => 'Counter is already serving a ticket'
            ]);
        }

        // Get the next waiting ticket
        $nextTicket = Ticket::where('status', 'waiting')
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$nextTicket) {
            return response()->json([
                'success' => false,
                'message' => 'No tickets in the waiting queue'
            ]);
        }

        $nextTicket->status = 'serving';
        $nextTicket->counter_id = $counter->id;
        $nextTicket->save();

        $counter->is_serving = true;
        $counter->current_ticket_number = $nextTicket->ticket_number;
        $counter->save();

        return response()->json([
            'success' => true,
            'counter' => $counter,
            'now_serving' => $nextTicket->ticket_number
        ]);
    }
}
