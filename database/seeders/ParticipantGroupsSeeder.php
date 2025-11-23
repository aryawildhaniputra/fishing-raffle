<?php

namespace Database\Seeders;

use App\Models\Participant;
use App\Models\ParticipantGroup;
use App\Support\Enums\ParticipantGroupRaffleStatusEnum;
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
                "status" => "paid",
                "total_member" => 3,
                "raffle_status" => ParticipantGroupRaffleStatusEnum::COMPLETED->value,
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Turen",
                "phone_num" => "082134546789",
                "event_id" => 1,
                "status" => "paid",
                "total_member" => 3,
                "raffle_status" => ParticipantGroupRaffleStatusEnum::NOT_YET->value,
                "created_at" => now(),
                "updated_at" => now(),
            ],
        ]);
    }
}
