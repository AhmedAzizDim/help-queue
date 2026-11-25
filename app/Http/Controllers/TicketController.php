<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(): View
    {
        return view('tickets.index', [
            'helping' => Ticket::with('topic')->helping()->get(),
            'waiting' => Ticket::with('topic')->waiting()->get(),
            'doneCount' => Ticket::where('status', 'done')->count(),
            'topics' => Topic::withCount('tickets')->orderBy('name')->get(),
        ]);
    }

    public function show(Ticket $ticket): View
    {
        $ahead = Ticket::waiting()->where('id', '<', $ticket->id)->count();

        return view('tickets.show', ['ticket' => $ticket, 'ahead' => $ahead]);
    }

    public function create(): View
    {
        return view('tickets.create', ['topics' => Topic::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'topic_id' => ['required', 'exists:topics,id'],
            'description' => ['nullable', 'string', 'max:500'],
        ], [], ['topic_id' => 'topic']);

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
