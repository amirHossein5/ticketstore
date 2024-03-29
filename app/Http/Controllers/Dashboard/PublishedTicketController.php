<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class PublishedTicketController extends Controller
{
    public function store()
    {
        $ticket = auth()->user()->tickets()
            ->whereUlid(request('ticket'))
            ->firstOrFail();

        $ticket->update(['published_at' => now()]);

        return to_route('dashboard.index');
    }
}
