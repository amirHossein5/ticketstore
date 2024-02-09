<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function show(Order $order): View
    {
        return view('orders.show', [
            'order' => $order->load('tickets'),
        ]);
    }

    public function create(Ticket $ticket): View|RedirectResponse
    {
        if ($ticket->sold_out) {
            abort(404);
        }

        return view('orders.create', compact('ticket'));
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

        for ($i = 1; $i <= $validated['quantity']; $i++) {
            $order->addTicket($ticket);
        }

        $url = URL::temporarySignedRoute(
            'orders.show',
            now()->addMinutes(30),
            ['order' => $order->code]
        );

        return redirect($url);
    }
}
