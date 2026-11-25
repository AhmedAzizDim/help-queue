@extends('layouts.app')

@section('title', 'Ticket — ' . $ticket->name)

@section('content')
    <h1>{{ $ticket->name }}</h1>
    <article class="ticket">
        <span class="topic">{{ $ticket->topic?->name ?? 'Unassigned' }}</span>
        <p>{{ $ticket->description }}</p>
        <p>Status: <strong>{{ $ticket->status }}</strong></p>
        <p>Asked {{ $ticket->created_at->diffForHumans() }}</p>
        @if ($ticket->status === 'waiting')
            <p><strong>{{ $ahead }}</strong> {{ $ahead === 1 ? 'person' : 'people' }} ahead of you.</p>
        @endif
    </article>

    @error('status')
        <p class="error" role="alert">{{ $message }}</p>
    @enderror

    @if ($ticket->status !== 'done')
        <form method="POST" action="{{ route('tickets.update', $ticket) }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="{{ $ticket->status === 'waiting' ? 'helping' : 'done' }}">
            <button type="submit">{{ $ticket->status === 'waiting' ? 'Start helping' : 'Mark as done' }}</button>
        </form>
    @endif

    <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" onsubmit="return confirm('Remove this ticket?')">
        @csrf
        @method('DELETE')
        <button type="submit">Remove</button>
    </form>

    <a href="{{ route('tickets.index') }}">Back to the queue</a>
@endsection
