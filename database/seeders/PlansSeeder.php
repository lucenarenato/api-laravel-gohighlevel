<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'lite',
                'description' => 'Perfect for new schools looking to start grow online and offline.',
                'order' => 1,
                'cycle' => 'month',
                'price' => 1,
                'setup' => 1000,
                'trial' => 0,
                'features' => ["Unlimited contacts","Unlimited staffs"],
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'scale',
                'description' => 'Ideal for schools and studios looking to rapidly scale.',
                'order' => 4,
                'cycle' => 'month',
                'price' => 499,
                'setup' => 1000,
                'trial' => 14,
                'features' => ["Unlimited contacts","Unlimited staffs"],
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'scale',
                'description' => 'Ideal for schools and studios looking to rapidly scale.',
                'order' => 4,
                'cycle' => 'annual',
                'price' => 4999,
                'setup' => 0,
                'trial' => 0,
                'features' => ["Unlimited contacts","Unlimited staffs"],
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'seo',
                'description' => 'All the advance marketing and business solution for a mature schools or studios.',
                'order' => 6,
                'cycle' => 'month',
                'price' => 999,
                'setup' => 0,
                'trial' => 0,
                'features' => ["Unlimited contacts","Unlimited staffs"],
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'seo',
                'description' => 'All the advance marketing and business solution for a mature schools or studios.',
                'order' => 6,
                'cycle' => 'annual',
                'price' => 9999,
                'setup' => 0,
                'trial' => 0,
                'features' => ["Unlimited contacts","Unlimited staffs"],
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'growth',
                'description' => 'All the advance marketing and business solution for a mature schools or studios.',
                'order' => 3,
                'cycle' => 'month',
                'price' => 449,
                'setup' => 1000,
                'trial' => 14,
                'features' => ["Unlimited contacts","Unlimited team members"],
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'essentials',
                'description' => 'Perfect for new looking to start growing online or offline.',
                'order' => 2,
                'cycle' => 'month',
                'price' => 299,
                'setup' => 1000,
                'trial' => 14,
                'features' => ["1,000 contacts","Up to 3 team members","Capture online reviews instantly","Text-base website chat"],
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        foreach($plans as $plan){
            $plan['features'] = json_encode($plan['features']);
            Plan::create($plan);
        }

    }
}
