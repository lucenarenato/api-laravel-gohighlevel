<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class MemberProgramsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $member_programs = [
            [
                'member_id' => 1,
                'program_id' => 1
            ],
            [
                'member_id' => 1,
                'program_id' => 2
            ],
            [
                'member_id' => 2,
                'program_id' => 1
            ],
            [
                'member_id' => 2,
                'program_id' => 2
            ]
        ];

        foreach($member_programs as $member_program){
            DB::table('member_programs')->insert([
                'member_id' => $member_program['member_id'],
                'program_id' => $member_program['program_id']
            ]);
        }

    }
}
