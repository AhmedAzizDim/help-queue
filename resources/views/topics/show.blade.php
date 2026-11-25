@extends('layouts.app')

@section('title', $topic->name)

@section('content')
    <h1>Topic: {{ $topic->name }}</h1>
    <p>{{ $tickets->count() }} {{ $tickets->count() === 1 ? 'ticket' : 'tickets' }}</p>
    @forelse ($tickets as $ticket)
        @include('tickets._card', ['ticket' => $ticket])
    @empty
        <p>No tickets for this topic yet.</p>
    @endforelse
    <a href="{{ route('tickets.index') }}">Back to the queue</a>
@endsection
