@extends('layouts.app')

@section('title', 'Ticket — ' . $ticket->name)

@section('content')
    <h1>{{ $ticket->name }}</h1>
    <article class="ticket">
    <strong>{{ $loop->iteration }}. {{ $ticket->name }}</strong>
    <span class="topic">{{ $ticket->topic }}</span>
    <p>{{ $ticket->description }}</p>
    <a href="{{ route('tickets.show', $ticket) }}">Details</a>
</article>
    <a href="{{ route('tickets.index') }}">Back to the queue</a>
@endsection
