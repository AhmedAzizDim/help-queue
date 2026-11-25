<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\Topic;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $topics = collect(['Git', 'Laravel', 'Blade', 'Database', 'Setup'])
            ->map(fn ($name) => Topic::create(['name' => $name]));

        Ticket::factory(8)->create(fn () => ['topic_id' => $topics->random()->id]);
    }
}
