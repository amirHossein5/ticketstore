<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Jobs\ProcessTicketImage;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function create(): View
    {
        return view('dashboard.tickets.create');
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $ticket = auth()->user()->tickets()->create(array_merge($request->validated(), [
            'price' => $request['price'] * 100,
            'image' => $request['image']?->store('ticket-posters', 'public'),
        ]));

        if ($ticket->image) {
            ProcessTicketImage::dispatch($ticket->image);
        }

        return redirect('/dashboard');
    }

    public function edit(Ticket $ticket): View|RedirectResponse
    {
        if ($ticket->isPublished()) {
            abort(404);
        }

        if ($ticket->user_id !== auth()->id()) {
            abort(404);
        }

        return view('dashboard.tickets.edit', compact('ticket'));
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        if ($ticket->isPublished()) {
            abort(404);
        }

        if ($ticket->user_id !== auth()->id()) {
            abort(404);
        }

        if ($ticket->image) {
            Storage::disk('public')->delete($ticket->image);
        }

        $ticket->update(array_merge($request->validated(), [
            'price' => $request['price'] * 100,
            'image' => $request['image']?->store('ticket-posters', 'public'),
        ]));

        if (isset($request['image'])) {
            ProcessTicketImage::dispatch($ticket->fresh()->image);
        }

        return redirect('/dashboard');
    }
}
