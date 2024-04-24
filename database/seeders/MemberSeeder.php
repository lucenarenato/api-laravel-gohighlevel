<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::orderBy('created_at', 'desc')->take(3)->get();

        $members = [
            [
                'name' => 'Admin',
                'email' => 'admin@monstro.com',
                'phone' => '+1789276565',
                'referral_code' => '88765',
                'user_id' => 1
            ],
            [
                'name' => 'Vendor One',
                'email' => 'vendor.one@monstro.com',
                'phone' => '+1789276565',
                'referral_code' => '88765',
                'user_id' => 2
            ],
            [
                'name' => 'Vendor Two',
                'email' => 'vendor.two@monstro.com',
                'phone' => '+1789276565',
                'referral_code' => '88765',
                'user_id' => 3
            ],
            [
                'name' => 'Alex',
                'email' => 'alex.gabreil@monstro.com',
                'phone' => '+1789276885',
                'referral_code' => '88764',
                'user_id' => 4
            ],
            [
                'name' => 'John',
                'email' => 'john.safari@monstro.com',
                'phone' => '+1756276885',
                'referral_code' => '88763',
                'user_id' => 5
            ],
        ];

        foreach ($members as $member) {
            $member = Member::create($member);
        }

        // foreach ($users as $index => $user) {
        //     if (isset($members[$index])) {
        //         $member = $members[$index];
                
        //         $user->member()->create([
        //             'name' => $member['name'],
        //             'email' => $member['email'],
        //             'phone' => $member['phone'],
        //             'referral_code' => $member['referral_code'],
        //         ]);
        //     }
        // }

    }
}
