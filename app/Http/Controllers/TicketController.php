<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(): View
    {
        $tickets = Ticket::published()
            ->latest()
            ->get();

        return view('tickets.index', [
            'tickets' => $tickets,
        ]);
    }
}
