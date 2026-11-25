<?php

use App\Models\Ticket;
use App\Models\Topic;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

test('a ticket belongs to its topic and a topic includes only its own tickets', function () {
    $topic = Topic::create(['name' => 'Laravel']);
    $other = Topic::create(['name' => 'Git']);
    $first = Ticket::factory()->create(['topic_id' => $topic->id]);
    $second = Ticket::factory()->create(['topic_id' => $topic->id]);
    Ticket::factory()->create(['topic_id' => $other->id]);

    expect($first->fresh()->topic->is($topic))->toBeTrue();
    expect($topic->tickets()->orderBy('id')->get()->modelKeys())->toBe([$first->id, $second->id]);
});

test('the database seeder creates five topics and eight tickets with valid topics', function () {
    $this->seed(DatabaseSeeder::class);

    expect(Topic::orderBy('name')->pluck('name')->all())->toBe(['Blade', 'Database', 'Git', 'Laravel', 'Setup']);
    $this->assertDatabaseCount('tickets', 8);
    expect(Ticket::whereNull('topic_id')->count())->toBe(0);
    expect(Ticket::has('topic')->count())->toBe(8);
    expect(Ticket::waiting()->count())->toBe(8);
});

test('a duplicate topic name is rejected', function () {
    Topic::create(['name' => 'Laravel']);

    expect(fn () => Topic::create(['name' => 'Laravel']))->toThrow(QueryException::class);
});

test('a ticket cannot refer to a topic that does not exist', function () {
    expect(fn () => Ticket::factory()->create(['topic_id' => 999]))->toThrow(QueryException::class);
});

test('the nullable topic relationship supports an unassigned ticket', function () {
    $ticket = Ticket::factory()->create()->fresh();

    expect($ticket->topic_id)->toBeNull();
    expect($ticket->topic)->toBeNull();
});

test('exercise four badges display the topic object as escaped JSON', function () {
    $topic = Topic::create(['name' => 'Laravel'])->fresh();
    $ticket = Ticket::factory()->create(['topic_id' => $topic->id]);

    foreach ([route('tickets.index'), route('tickets.show', $ticket)] as $url) {
        $this->get($url)->assertOk()->assertSee($topic->toJson());
    }
});

test('the topic id migration can be rolled back and reapplied', function () {
    $topic = Topic::create(['name' => 'Git']);
    $ticket = Ticket::factory()->create(['topic_id' => $topic->id]);
    $migration = require database_path('migrations/2026_11_25_101810_add_topic_id_to_tickets_table.php');

    $migration->down();

    expect(Schema::hasColumn('tickets', 'topic'))->toBeTrue();
    expect(Schema::hasColumn('tickets', 'topic_id'))->toBeFalse();
    $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'topic' => '']);

    $migration->up();

    expect(Schema::hasColumn('tickets', 'topic'))->toBeFalse();
    expect(Schema::hasColumn('tickets', 'topic_id'))->toBeTrue();
    $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'topic_id' => null]);
});
