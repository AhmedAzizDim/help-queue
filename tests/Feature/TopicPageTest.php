<?php

use App\Models\Ticket;
use App\Models\Topic;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a topic page lists only its own tickets in id order including all statuses', function () {
    $topic = Topic::create(['name' => 'Laravel']);
    $other = Topic::create(['name' => 'Git']);
    Ticket::factory()->create(['name' => 'Waiting person', 'topic_id' => $topic->id]);
    Ticket::factory()->create(['name' => 'Other topic person', 'topic_id' => $other->id]);
    Ticket::factory()->create(['name' => 'Helped person', 'topic_id' => $topic->id, 'status' => 'helping']);
    Ticket::factory()->create(['name' => 'Finished person', 'topic_id' => $topic->id, 'status' => 'done']);

    $this->get(route('topics.show', $topic))->assertOk()
        ->assertSee('Topic: Laravel')->assertSee('3 tickets')
        ->assertSeeInOrder(['Waiting person', 'Helped person', 'Finished person'])
        ->assertDontSee('Other topic person')
        ->assertViewHas('tickets', fn ($tickets) => $tickets->every(fn ($ticket) => $ticket->relationLoaded('topic')));
});

test('a topic page shows singular and empty counts', function () {
    $topic = Topic::create(['name' => 'Blade']);

    $this->get(route('topics.show', $topic))->assertOk()
        ->assertSee('0 tickets')->assertSee('No tickets for this topic yet.');

    Ticket::factory()->create(['topic_id' => $topic->id]);

    $this->get(route('topics.show', $topic))->assertOk()->assertSee('<p>1 ticket</p>', false);
});

test('a nonexistent topic returns a model binding 404', function () {
    $this->get('/topics/999')->assertNotFound();
});

test('queue cards link to the correct topic and topic totals include completed tickets', function () {
    $topic = Topic::create(['name' => 'Git']);
    Topic::create(['name' => 'Setup']);
    Ticket::factory()->create(['topic_id' => $topic->id]);
    Ticket::factory()->create(['topic_id' => $topic->id, 'status' => 'done']);

    $this->get(route('tickets.index'))->assertOk()
        ->assertSee('<a class="topic" href="'.route('topics.show', $topic).'">Git</a>', false)
        ->assertSee('Git (2 tickets)')->assertSee('Setup (0 tickets)');
});
