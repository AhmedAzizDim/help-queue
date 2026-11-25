<article class="ticket">
    <strong>{{ isset($position) ? $position . '. ' : '' }}{{ $ticket->name }}</strong>
    <span class="topic">{{ $ticket->topic }}</span>
    <p>{{ $ticket->description }}</p>
    <a href="{{ route('tickets.show', $ticket) }}">Details</a>
</article>
