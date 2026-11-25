<?php

use App\Models\Ticket;
use App\Models\Topic;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the json queue includes only waiting tickets in id order with nested topics', function () {
    $topic = Topic::create(['name' => 'Git']);
    $first = Ticket::factory()->create(['topic_id' => $topic->id, 'created_at' => now()]);
    Ticket::factory()->create(['topic_id' => $topic->id, 'status' => 'helping']);
    Ticket::factory()->create(['topic_id' => $topic->id, 'status' => 'done']);
    $last = Ticket::factory()->create(['topic_id' => $topic->id, 'created_at' => now()->subDay()]);

    $this->get('/queue.json')->assertOk()
        ->assertHeader('Content-Type', 'application/json')
        ->assertJsonCount(2)
        ->assertJsonPath('0.id', $first->id)->assertJsonPath('1.id', $last->id)
        ->assertJsonPath('0.status', 'waiting')->assertJsonPath('1.status', 'waiting')
        ->assertJsonPath('0.topic.id', $topic->id)->assertJsonPath('0.topic.name', 'Git')
        ->assertJsonStructure(['*' => [
            'id', 'name', 'description', 'status', 'topic_id', 'created_at', 'updated_at',
            'topic' => ['id', 'name', 'created_at', 'updated_at'],
        ]]);
});

test('an empty waiting queue returns an empty json array', function () {
    Ticket::factory()->create(['status' => 'done']);

    $this->get('/queue.json')->assertOk()->assertExactJson([]);
});

test('json supports an unassigned ticket without a broken response', function () {
    Ticket::factory()->create();

    $this->get('/queue.json')->assertOk()->assertJsonCount(1)
        ->assertJsonPath('0.topic_id', null)->assertJsonPath('0.topic', null);
});
