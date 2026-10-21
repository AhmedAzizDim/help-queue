<?php

use App\Models\Ticket;
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
        ->assertSee('0 waiting')
        ->assertSee('Nobody is waiting.');
});

test('tickets are read from the database in arrival order', function () {
    $later = Ticket::create(['name' => 'Sara', 'topic' => 'Setup']);
    $later->created_at = now();
    $later->save();
    $earlier = Ticket::create(['name' => 'Lina', 'topic' => 'Git', 'description' => 'My push is rejected']);
    $earlier->created_at = now()->subMinute();
    $earlier->save();

    $this->get('/tickets')->assertOk()
        ->assertSee('2 waiting')
        ->assertSeeInOrder(['1. Lina', 'Git', 'My push is rejected', '2. Sara', 'Setup'])
        ->assertSee(route('tickets.show', $earlier), false);

    Ticket::create(['name' => 'Omar', 'topic' => 'Laravel']);
    $this->get('/tickets')->assertOk()->assertSee('3 waiting')->assertSee('Omar');
});

test('ticket fields escape HTML in both views', function () {
    $ticket = Ticket::create([
        'name' => '<script>alert(1)</script>',
        'topic' => '<b>Git</b>',
        'description' => '<img src=x onerror=alert(1)>',
    ]);

    foreach (['/tickets', '/tickets/'.$ticket->id] as $url) {
        $this->get($url)->assertOk()
            ->assertSee($ticket->name)
            ->assertSee($ticket->topic)
            ->assertSee($ticket->description)
            ->assertDontSee('<script>', false)
            ->assertDontSee('<img src=x', false);
    }
});

test('ticket defaults and permitted fields match the assignment', function () {
    $ticket = Ticket::create(['name' => 'Omar', 'topic' => 'Laravel'])->fresh();

    expect($ticket->status)->toBe('waiting')
        ->and($ticket->description)->toBeNull()
        ->and($ticket->getFillable())->toBe(['name', 'topic', 'description'])
        ->and($ticket->isFillable('status'))->toBeFalse();
});

test('a ticket detail shows its fields and relative arrival time', function () {
    $ticket = Ticket::create(['name' => 'Lina', 'topic' => 'Git', 'description' => 'My push is rejected']);
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
    $this->assertDatabaseHas('tickets', ['name' => 'Lina', 'topic' => 'Git', 'status' => 'waiting']);
    $this->assertDatabaseHas('tickets', ['name' => 'Omar', 'description' => null]);
    $this->get('/tickets')->assertOk()->assertSee('3 waiting')->assertSeeInOrder(['1. Lina', '2. Omar', '3. Sara']);
});
