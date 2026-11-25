<article class="ticket">
    <strong>{{ isset($position) ? $position . '. ' : '' }}{{ $ticket->name }}</strong>
    @if ($ticket->topic)
        <a class="topic" href="{{ route('topics.show', $ticket->topic) }}">{{ $ticket->topic->name }}</a>
    @else
        <span class="topic">Unassigned</span>
    @endif
    <p>{{ $ticket->description }}</p>
    <a href="{{ route('tickets.show', $ticket) }}">Details</a>
</article>
