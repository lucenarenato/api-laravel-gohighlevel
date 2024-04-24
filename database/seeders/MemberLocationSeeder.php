<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Member;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class MemberLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $members = Member::orderBy('id', 'desc')->take(3)->get();
        $locations = Location::take(3)->get();

        foreach ($members as $member) {
            foreach ($locations as $location) {
                DB::table('member_locations')->insert([
                    'go_high_level_location_id' => 987654321,
                    'member_id' => $member->id,
                    'location_id' => $location->id
                ]);
            }
        }
    }
}
