<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function create(Ticket $ticket): View|RedirectResponse
    {
        $quantity = request('quantity');

        if (!is_numeric($quantity)) {
            return redirect()->route('purchase', [
                'ticket' => $ticket->ulid,
                'quantity' => 1
            ]);
        }

        return view('orders.create', compact('ticket', 'quantity'));
    }

    public function store(StoreOrderRequest $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validated();

        $ticket->update([
            'quantity' => $ticket->quantity - $validated['quantity'],
            'sold_count' => $ticket->sold_count + $validated['quantity'],
        ]);

        $order = Order::create([
            'email' => $validated['email'],
            'quantity' => $validated['quantity'],
            'charged' => $ticket->price * $validated['quantity'],
            'last_4' => substr($validated['card_number'], -4),
        ]);

        return to_route('orders.show', ['order' => $order]);
    }
}
