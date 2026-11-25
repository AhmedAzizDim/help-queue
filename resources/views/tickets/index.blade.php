@extends('layouts.app')

@section('title', 'Queue')

@section('content')
<h1>Help queue</h1>
<p>{{ $tickets->count() }} waiting</p>

@forelse ($tickets as $ticket)
<article class="ticket">
<strong>{{ $loop->iteration }}. {{ $ticket->name }}</strong>
<span class="topic">{{ $ticket->topic }}</span>
<p>{{ $ticket->description }}</p>
<a href="{{ route('tickets.show', $ticket) }}">Details</a>
</article>
@empty
<p>Nobody is waiting.</p>
@endforelse
@endsection
