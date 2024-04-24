<?php

namespace Database\Seeders;

use App\Models\Session;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sessions = [
            [
                'program_level_id' => 1,
                'duration_time' => 60,
                'start_date' => '2023-11-01',
                'end_date' => '2023-11-30',
                'monday' => '13:00:00',
                'tuesday' => '13:00:00',
                'wednesday' => '13:00:00',
                'thursday' => '13:00:00',
                'friday' => '13:00:00',
                'saturday' => '13:00:00',
                'sunday' => '13:00:00',
                'status' => 1
            ],
            [
                'program_level_id' => 2,
                'duration_time' => 60,
                'start_date' => '2023-11-01',
                'end_date' => '2023-11-30',
                'monday' => '13:00:00',
                'tuesday' => '13:00:00',
                'wednesday' => '13:00:00',
                'thursday' => '13:00:00',
                'friday' => '13:00:00',
                'saturday' => '13:00:00',
                'sunday' => '13:00:00',
                'status' => 1
            ],
            [
                'program_level_id' => 1,
                'duration_time' => 60,
                'start_date' => '2023-11-01',
                'end_date' => '2023-11-30',
                'monday' => '13:00:00',
                'tuesday' => '13:00:00',
                'wednesday' => '13:00:00',
                'thursday' => '13:00:00',
                'friday' => '13:00:00',
                'saturday' => '13:00:00',
                'sunday' => '13:00:00',
                'status' => 1
            ],
            [
                'program_level_id' => 1,
                'duration_time' => 60,
                'start_date' => '2023-11-01',
                'end_date' => '2023-11-30',
                'monday' => '13:00:00',
                'tuesday' => '13:00:00',
                'wednesday' => '13:00:00',
                'thursday' => '13:00:00',
                'friday' => '13:00:00',
                'saturday' => '13:00:00',
                'sunday' => '13:00:00',
                'status' => 1
            ],
        ];

        foreach($sessions as $session){
            Session::insert($session);
        }
        
    }
}
