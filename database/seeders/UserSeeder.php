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
            'email' => 'admin@teste.com',
            'password' => bcrypt('admin123!'),
            'email_verified_at' => now()
        ]);

        $admin->assignRole(\App\Models\User::ADMIN);

        $vendors = [
            [
                'name' => 'Vendor One',
                'email' => 'vendor.one@teste.com',
                'password' => bcrypt('vendor123!'),
                'email_verified_at' => now()
            ],
            [
                'name' => 'Vendor Two',
                'email' => 'vendor.two@teste.com',
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
                'email' => 'alex.gabreil@teste.com',
                'password' => bcrypt('member123!'),
                'email_verified_at' => now()
            ],
            [
                'name' => 'John',
                'email' => 'john.safari@teste.com',
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
