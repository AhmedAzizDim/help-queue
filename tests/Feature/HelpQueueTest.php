<?php

use App\Models\Ticket;
use App\Models\Topic;
use Database\Seeders\TicketSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the hello route returns the required sentence', function () {
    $this->get('/hello')->assertOk()->assertContent('Hello from Laravel!');
});

test('the hello view accepts any single name segment', function () {
    $this->get('/hello/Souhail')->assertOk()->assertSee('<h1>Hello Souhail!</h1>', false);
    $this->get('/hello/aziz')->assertOk()->assertSee('Hello aziz!');
    $this->get('/helo')->assertNotFound();
    $this->get('/hello/a/b')->assertNotFound();
});

test('the hello view escapes HTML in the name', function () {
    $this->get('/hello/'.rawurlencode('<marquee>Bob'))
        ->assertOk()
        ->assertSee('&lt;marquee&gt;Bob', false)
        ->assertDontSee('<marquee>', false);
});

test('an empty queue uses the shared layout and empty message', function () {
    $this->get('/tickets')->assertOk()
        ->assertSee('<title>Queue</title>', false)
        ->assertSee('Help Queue')
        ->assertSee('Being helped')
        ->assertSee('Nobody right now.')
        ->assertSee('Waiting (0)')
        ->assertSee('0 solved so far.')
        ->assertSee('Nobody is waiting.');
});

test('waiting tickets are numbered by id even when timestamps are out of order', function () {
    $this->freezeTime();
    $later = Ticket::create(['name' => 'Sara', 'topic_id' => Topic::firstOrCreate(['name' => 'Setup'])->id]);
    $later->created_at = now();
    $later->save();
    $earlier = Ticket::create(['name' => 'Lina', 'topic_id' => Topic::firstOrCreate(['name' => 'Git'])->id, 'description' => 'My push is rejected']);
    $earlier->created_at = now()->subMinute();
    $earlier->save();

    $this->get('/tickets')->assertOk()
        ->assertSee('Waiting (2)')
        ->assertSeeInOrder(['1. Sara', 'Setup', '2. Lina', 'Git', 'My push is rejected'])
        ->assertSee(route('tickets.show', $earlier), false);

    Ticket::create(['name' => 'Omar', 'topic_id' => Topic::firstOrCreate(['name' => 'Laravel'])->id]);
    $this->get('/tickets')->assertOk()->assertSee('Waiting (3)')->assertSee('Omar');
});

test('ticket fields escape HTML in both views', function () {
    $ticket = Ticket::create([
        'name' => '<script>alert(1)</script>',
        'topic_id' => Topic::firstOrCreate(['name' => '<b>Git</b>'])->id,
        'description' => '<img src=x onerror=alert(1)>',
    ]);

    foreach (['/tickets', '/tickets/'.$ticket->id] as $url) {
        $this->get($url)->assertOk()
            ->assertSee($ticket->name)
            ->assertSee($ticket->topic->toJson())
            ->assertSee($ticket->description)
            ->assertDontSee('<script>', false)
            ->assertDontSee('<img src=x', false);
    }
});

test('ticket defaults and permitted fields match the assignment', function () {
    $ticket = Ticket::create(['name' => 'Omar', 'topic_id' => Topic::firstOrCreate(['name' => 'Laravel'])->id])->fresh();

    expect($ticket->status)->toBe('waiting')
        ->and($ticket->description)->toBeNull()
        ->and($ticket->getFillable())->toBe(['name', 'topic_id', 'description'])
        ->and($ticket->isFillable('status'))->toBeFalse();
});

test('a ticket detail shows its fields and relative arrival time', function () {
    $ticket = Ticket::create(['name' => 'Lina', 'topic_id' => Topic::firstOrCreate(['name' => 'Git'])->id, 'description' => 'My push is rejected']);
    $ticket->created_at = now()->subMinutes(5);
    $ticket->save();

    $this->get('/tickets/'.$ticket->id)->assertOk()
        ->assertSee('Lina')->assertSee('Git')->assertSee('My push is rejected')
        ->assertSee('5 minutes ago')->assertSee('Back to the queue');
});

test('a missing ticket returns a model binding 404', function () {
    $this->get('/tickets/999')->assertNotFound();
});

test('the assignment sample tickets can be seeded without duplicates', function () {
    $this->seed(TicketSeeder::class);
    $this->seed(TicketSeeder::class);

    expect(Ticket::count())->toBe(3);
    $this->assertDatabaseHas('tickets', ['name' => 'Lina', 'topic_id' => Topic::firstOrCreate(['name' => 'Git'])->id, 'status' => 'waiting']);
    $this->assertDatabaseHas('tickets', ['name' => 'Omar', 'description' => null]);
    $this->get('/tickets')->assertOk()->assertSee('Waiting (3)')->assertSeeInOrder(['1. Lina', '2. Omar', '3. Sara']);
});

test('the queue separates helping and waiting tickets and counts completed tickets', function () {
    Ticket::factory()->create(['name' => 'Amina']);
    Ticket::factory()->create(['name' => 'Bilal', 'status' => 'helping']);
    Ticket::factory()->create(['name' => 'Completed ticket', 'status' => 'done']);
    Ticket::factory()->create(['name' => 'Dina']);
    Ticket::factory()->create(['name' => 'Other completed ticket', 'status' => 'done']);

    $this->get('/tickets')->assertOk()
        ->assertSeeInOrder([
            '<h2>Being helped</h2>',
            '<strong>Bilal</strong>',
            '<h2>Waiting (2)</h2>',
            '<strong>1. Amina</strong>',
            '<strong>2. Dina</strong>',
            '2 solved so far.',
        ], false)
        ->assertDontSee('Completed ticket')
        ->assertDontSee('Other completed ticket')
        ->assertDontSee('Nobody right now.')
        ->assertDontSee('Nobody is waiting.');
});

test('starting help moves a ticket from the waiting list into being helped', function () {
    $ticket = Ticket::factory()->create(['name' => 'Omar']);

    $this->patch(route('tickets.update', $ticket), ['status' => 'helping'])
        ->assertRedirect(route('tickets.show', $ticket));

    $this->get('/tickets')->assertOk()
        ->assertSeeInOrder(['Being helped', 'Omar', 'Waiting (0)', 'Nobody is waiting.', '0 solved so far.'])
        ->assertDontSee('1. Omar')
        ->assertDontSee('Nobody right now.');
});

test('finishing help removes the ticket card and increases the solved count', function () {
    $ticket = Ticket::factory()->create(['name' => 'Finished ticket', 'status' => 'helping']);

    $this->patch(route('tickets.update', $ticket), ['status' => 'done'])
        ->assertRedirect(route('tickets.show', $ticket));

    $this->get('/tickets')->assertOk()
        ->assertSee('Nobody right now.')
        ->assertSee('Waiting (0)')
        ->assertSee('Nobody is waiting.')
        ->assertSee('1 solved so far.')
        ->assertDontSee('Finished ticket');
});

test('a waiting ticket displays how many people are ahead with the correct wording', function (int $count, string $noun) {
    Ticket::factory()->count($count)->create();
    $ticket = Ticket::factory()->create();
    Ticket::factory()->create();

    $this->get(route('tickets.show', $ticket))->assertOk()
        ->assertViewHas('ahead', $count)
        ->assertSee("<strong>{$count}</strong> {$noun} ahead of you.", false);
})->with([
    'first in line' => [0, 'people'],
    'one person ahead' => [1, 'person'],
    'several people ahead' => [2, 'people'],
]);

test('queue position counts only waiting tickets with smaller ids regardless of timestamps', function () {
    $this->freezeTime();
    Ticket::factory()->create(['created_at' => now()->addMinute()]);
    Ticket::factory()->create(['status' => 'helping']);
    Ticket::factory()->create(['status' => 'done']);
    $ticket = Ticket::factory()->create();
    Ticket::factory()->create(['created_at' => now()->subMinute()]);

    $this->get(route('tickets.show', $ticket))->assertOk()
        ->assertViewHas('ahead', 1)
        ->assertSee('<strong>1</strong> person ahead of you.', false);
});

test('queue position is hidden when a ticket is no longer waiting', function (string $status) {
    Ticket::factory()->create();
    $ticket = Ticket::factory()->create(['status' => $status]);

    $this->get(route('tickets.show', $ticket))->assertOk()
        ->assertDontSee('ahead of you.');
})->with(['helping', 'done']);

test('queue position decreases when an earlier ticket starts being helped', function () {
    $ahead = Ticket::factory()->create();
    $ticket = Ticket::factory()->create();
    $this->get(route('tickets.show', $ticket))->assertViewHas('ahead', 1);

    $this->patch(route('tickets.update', $ahead), ['status' => 'helping'])
        ->assertRedirect(route('tickets.show', $ahead));

    $this->get(route('tickets.show', $ticket))->assertOk()
        ->assertViewHas('ahead', 0)
        ->assertSee('<strong>0</strong> people ahead of you.', false);
});
