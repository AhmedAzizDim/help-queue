@extends('layouts.app')
@section('title', 'Queue')
@section('content')
<h1>Help queue</h1>
<p>{{ $tickets->count() }} waiting</p>

@forelse ($tickets as $ticket)
    <article class="ticket">
        <strong><a href="{{ route('tickets.show', $ticket) }}">{{ $loop->iteration }}. {{ $ticket->name }}</a></strong>
        <span class="topic">{{ $ticket->topic }}</span>
        <p>{{ $ticket->description }}</p>
    </article>
@empty
    <p>Nobody is waiting.</p>
@endforelse
@endsection
