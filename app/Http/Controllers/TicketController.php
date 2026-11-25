<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(): View
    {
        return view('tickets.index', [
            'helping' => Ticket::helping()->get(),
            'waiting' => Ticket::waiting()->get(),
            'doneCount' => Ticket::where('status', 'done')->count(),
        ]);
    }

    public function show(Ticket $ticket): View
    {
        return view('tickets.show', ['ticket' => $ticket]);
    }

    public function create(): View
    {
        return view('tickets.create', ['topics' => Ticket::TOPICS]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'topic' => ['required', Rule::in(Ticket::TOPICS)],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        Ticket::create($validated);

        return redirect()->route('tickets.index')->with('message', 'You are in the queue!');
    }

    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(Ticket::STATUSES)],
        ]);

        $ticket->status = $validated['status'];
        $ticket->save();

        return redirect()->route('tickets.show', $ticket)->with('message', 'Status updated.');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        $ticket->delete();

        return redirect()->route('tickets.index')->with('message', 'Ticket removed.');
    }
}
