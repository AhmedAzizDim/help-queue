<?php

use App\Models\Ticket;
use App\Models\Topic;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a ticket progresses from waiting to helping to done through the form', function () {
    $ticket = Ticket::factory()->create();
    $url = route('tickets.show', $ticket);

    $this->get($url)
        ->assertSee('Start helping')
        ->assertSee('name="status" value="helping"', false)
        ->assertDontSee('Mark as done');

    $this->post(route('tickets.update', $ticket), ['_method' => 'PATCH', 'status' => 'helping'])
        ->assertRedirect($url)
        ->assertSessionHas('message', 'Status updated.');
    $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'status' => 'helping']);

    $this->get($url)
        ->assertSee('Mark as done')
        ->assertSee('name="status" value="done"', false)
        ->assertDontSee('Start helping');

    $this->post(route('tickets.update', $ticket), ['_method' => 'PATCH', 'status' => 'done'])
        ->assertRedirect($url)
        ->assertSessionHas('message', 'Status updated.');
    $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'status' => 'done']);

    $this->get($url)
        ->assertDontSee('Start helping')
        ->assertDontSee('Mark as done')
        ->assertDontSee('name="status"', false)
        ->assertSee('Remove');
});

test('an invalid status shows the validation message without changing the ticket', function () {
    $ticket = Ticket::factory()->create();
    $url = route('tickets.show', $ticket);

    $this->from($url)->patch(route('tickets.update', $ticket), ['status' => 'sleeping'])
        ->assertRedirect($url)
        ->assertSessionHasErrors(['status' => 'The selected status is invalid.']);

    $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'status' => 'waiting']);
    $this->withCookie(config('session.cookie'), session()->getId())
        ->get($url)
        ->assertSee('The selected status is invalid.')
        ->assertSee('Start helping');
});

test('a missing status is rejected without changing the ticket', function () {
    $ticket = Ticket::factory()->create();

    $this->from(route('tickets.show', $ticket))->patch(route('tickets.update', $ticket), [])
        ->assertRedirect(route('tickets.show', $ticket))
        ->assertSessionHasErrors(['status' => 'The status field is required.']);

    $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'status' => 'waiting']);
});

test('a status update changes only the status field', function () {
    $ticket = Ticket::factory()->create([
        'name' => 'Lina', 'topic_id' => Topic::firstOrCreate(['name' => 'Git'])->id, 'description' => 'My push is rejected',
    ]);

    $this->patch(route('tickets.update', $ticket), [
        'status' => 'helping', 'name' => 'Changed', 'topic_id' => Topic::firstOrCreate(['name' => 'Setup'])->id, 'description' => 'Changed',
    ])->assertRedirect(route('tickets.show', $ticket));

    $this->assertDatabaseHas('tickets', [
        'id' => $ticket->id, 'status' => 'helping',
        'name' => 'Lina', 'topic_id' => Topic::firstOrCreate(['name' => 'Git'])->id, 'description' => 'My push is rejected',
    ]);
});

test('a status update for a missing ticket returns 404', function () {
    $this->patch('/tickets/999', ['status' => 'helping'])->assertNotFound();
    $this->assertDatabaseCount('tickets', 0);
});

test('new tickets start waiting even when an extra status is submitted', function () {
    $this->post(route('tickets.store'), [
        'name' => 'Lina', 'topic_id' => Topic::firstOrCreate(['name' => 'Git'])->id, 'status' => 'done',
    ])->assertRedirect(route('tickets.index'))
        ->assertSessionHas('message', 'You are in the queue!');

    $this->assertDatabaseHas('tickets', ['name' => 'Lina', 'topic_id' => Topic::firstOrCreate(['name' => 'Git'])->id, 'status' => 'waiting']);
});

test('a ticket can still be removed through its separate delete form', function () {
    $ticket = Ticket::factory()->create();

    $this->post(route('tickets.destroy', $ticket), ['_method' => 'DELETE'])
        ->assertRedirect(route('tickets.index'))
        ->assertSessionHas('message', 'Ticket removed.');

    $this->assertModelMissing($ticket);
});
