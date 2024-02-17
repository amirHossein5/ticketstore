<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Mail\AttendeeMessageMail;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AttendeeMessageController extends Controller
{
    public function create(Ticket $ticket): View|RedirectResponse
    {
        if ($ticket->user_id !== auth()->id()) {
            abort(404);
        }

        if (! $ticket->isPublished()) {
            abort(404);
        }

        return view('dashboard.published-tickets.attendee-message', compact('ticket'));
    }

    public function store(Request $request, Ticket $ticket): RedirectResponse
    {
        if ($ticket->user_id !== auth()->id()) {
            abort(404);
        }

        if (! $ticket->isPublished()) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => ['required'],
            'body' => ['required'],
        ]);

        $message = $ticket->attendeeMessages()->create($validated);

        $ticket->chunkAttendeeEmails(20, function ($email) use ($message) {
            Mail::to($email)->queue(new AttendeeMessageMail($message));
        });

        return redirect('/dashboard')->with('message', 'Message sent successfully');
    }
}
