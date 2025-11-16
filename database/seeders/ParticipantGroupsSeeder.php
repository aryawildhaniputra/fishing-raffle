<?php

namespace Database\Seeders;

use App\Models\Participant;
use App\Models\ParticipantGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ParticipantGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ParticipantGroup::insert([
            [
                "name" => "Gondanglegi",
                "phone_num" => "082134546789",
                "event_id" => 1,
                "status" => "unpaid",
                "total_member" => 3,
            ],
        ]);
    }
}
