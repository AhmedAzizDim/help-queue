@extends('layouts.app')

@section('title', 'Ticket — ' . $ticket->name)

@section('content')
    <h1>{{ $ticket->name }}</h1>
    <article class="ticket">
        <span class="topic">{{ $ticket->topic }}</span>
        <p>{{ $ticket->description }}</p>
        <p>Requested {{ $ticket->created_at->diffForHumans() }}</p>
    </article>
    <a href="{{ route('tickets.index') }}">Back to the queue</a>
@endsection
