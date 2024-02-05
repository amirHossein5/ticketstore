<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(): View
    {
        $tickets = Ticket::orderBy('created_at', 'desc')
            ->whereNotNull('published_at')
            ->get();

        return view('tickets.index', [
            'tickets' => $tickets,
        ]);
    }
}
