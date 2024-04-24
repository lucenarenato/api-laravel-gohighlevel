<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@monstro.com',
            'password' => bcrypt('admin123!'),
            'email_verified_at' => now()
        ]);

        $admin->assignRole(\App\Models\User::ADMIN);

        $vendors = [
            [
                'name' => 'Vendor One',
                'email' => 'vendor.one@monstro.com',
                'password' => bcrypt('vendor123!'),
                'email_verified_at' => now()
            ],
            [
                'name' => 'Vendor Two',
                'email' => 'vendor.two@monstro.com',
                'password' => bcrypt('vendor123!'),
                'email_verified_at' => now()
            ]
        ];
    
        foreach ($vendors as $vendor) {
            $vendor = User::create($vendor);
            if ($vendor) {
                $vendor->assignRole(\App\Models\User::VENDOR);
            }
        }

        $users = [
            [
                'name' => 'Alex',
                'email' => 'alex.gabreil@monstro.com',
                'password' => bcrypt('member123!'),
                'email_verified_at' => now()
            ],
            [
                'name' => 'John',
                'email' => 'john.safari@monstro.com',
                'password' => bcrypt('member123!'),
                'email_verified_at' => now()
            ]
        ];
    
        foreach ($users as $user) {
            $user = User::create($user);
            if ($user) {
                $user->assignRole(\App\Models\User::MEMBER);
            }
        }
    }

}