<?php

namespace Database\Seeders;

use App\Models\CheckIn;
use Illuminate\Database\Seeder;

class CheckInSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $check_ins = [
            [
                'reservation_id' => 1,
                'check_in_time' => '2023-11-20 01:30:00',
                'check_out_time' => '2023-11-20 02:00:00'
            ],
            [
                'reservation_id' => 1,
                'check_in_time' => '2023-11-21 01:30:00',
                'check_out_time' => '2023-11-21 02:00:00'
            ],
            [
                'reservation_id' => 1,
                'check_in_time' => '2023-11-22 01:30:00',
                'check_out_time' => '2023-11-22 02:00:00'
            ],
            [
                'reservation_id' => 1,
                'check_in_time' => '2023-11-23 01:30:00',
                'check_out_time' => '2023-11-23 02:00:00'
            ],
            [
                'reservation_id' => 5,
                'check_in_time' => '2023-11-20 01:30:00',
                'check_out_time' => '2023-11-20 02:00:00'
            ],
            [
                'reservation_id' => 6,
                'check_in_time' => '2023-11-20 01:30:00',
                'check_out_time' => '2023-11-20 02:00:00'
            ],
            [
                'reservation_id' => 7,
                'check_in_time' => '2023-11-20 01:30:00',
                'check_out_time' => '2023-11-20 02:00:00'
            ],
            [
                'reservation_id' => 8,
                'check_in_time' => '2023-11-20 01:30:00',
                'check_out_time' => '2023-11-20 02:00:00'
            ],
        ];

        foreach($check_ins as $check_in){
            CheckIn::insert($check_in);
        }

    }
}
