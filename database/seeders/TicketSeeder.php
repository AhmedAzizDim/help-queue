<?php

namespace Database\Seeders;

use App\Models\Ticket;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'Lina', 'topic' => 'Git', 'description' => 'My push is rejected'],
            ['name' => 'Omar', 'topic' => 'Laravel', 'description' => null],
            ['name' => 'Sara', 'topic' => 'Setup', 'description' => 'php is not recognized'],
        ] as $ticket) {
            Ticket::firstOrCreate($ticket);
        }
    }
}
