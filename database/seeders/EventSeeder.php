<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Event::insert([
            [
                "name" => "Lomba Opening Pemancingan",
                "event_date" => now(),
                "price" => 250000,
                "total_stalls" => 211,
                "total_registrant" => 10,
            ],
        ]);
    }
}
