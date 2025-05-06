<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Counter;
use App\Models\Ticket;
use App\Events\CounterStatusChanged;
use App\Events\TicketCalled;

class CounterController extends Controller
{
    public function index()
    {
        $counters = Counter::all();

        if ($counters->count() == 0) {
            // Create 4 counters if none exist
            for ($i = 1; $i <= 4; $i++) {
                Counter::create([
                    'is_online' => true,
                    'is_serving' => false
                ]);
            }
            $counters = Counter::all();
        }

        return view('counter.index', compact('counters'));
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

        event(new CounterStatusChanged($counter));

        return response()->json([
            'status' => $counter->is_online ? 'online' : 'offline'
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
            $counter->save();

            event(new CounterStatusChanged($counter));

            return response()->json([
                'success' => true
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

        // Get now serving number
        $nowServing = $nextTicket->ticket_number;

        event(new TicketCalled($nextTicket, $counter, $nowServing));
        event(new CounterStatusChanged($counter));

        return response()->json([
            'success' => true,
            'ticket_number' => $nextTicket->ticket_number
        ]);
    }
}
