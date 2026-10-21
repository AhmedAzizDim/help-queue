<?php

namespace App\Http\Controllers;

use App\Models\Ticket;

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

}
