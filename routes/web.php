<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::orderBy('created_at')->get();
        return view('tickets.index', ['tickets' => $tickets]);
    }

    public function show(Ticket $ticket)
    {
        return view('tickets.show', ['ticket' => $ticket]);
    }

    public function create()
    {
        return view('tickets.create', ['topics' => Ticket::TOPICS]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'topic' => ['required', Rule::in(Ticket::TOPICS)],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        Ticket::create($validated);

        return redirect()->route('tickets.index')->with('message', 'You are in the queue!');
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return redirect()->route('tickets.index')->with('message', 'Ticket removed.');
    }
}