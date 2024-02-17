<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PublishedTicketOrdersController extends Controller
{
    public function index(Ticket $ticket): View|RedirectResponse
    {
        if (! $ticket->isPublished()) {
            abort(404);
        }

        if ($ticket->user_id !== auth()->id()) {
            abort(404);
        }

        return view('dashboard.published-tickets.orders', [
            'ticket' => $ticket,
            'orders' => $ticket->orders()->latest()->take(10)->get(),
            'soldOutPercentage' => $ticket->soldOutPercentage(),
            'totalRevenueInDollars' => $ticket->totalRevenueInDollars(),
        ]);
    }
}
