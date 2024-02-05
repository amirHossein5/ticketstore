<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function create(string $ticketUlid): View|RedirectResponse
    {
        $quantity = request('quantity');
        $ticket = Ticket::whereUlid($ticketUlid)
            ->published()
            ->firstOrFail();

        if (!is_numeric($quantity)) {
            return redirect()->route('purchase', [
                'ticket_ulid' => $ticket->ulid,
                'quantity' => 1
            ]);
        }

        return view('orders.create', compact('ticket', 'quantity'));
    }
}
