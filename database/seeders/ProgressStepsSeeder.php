<?php

namespace Database\Seeders;

use App\Models\ProgressStep;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProgressStepsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $progress_steps = [
            [
                'name' => 'Start Here',
                'orders' => 1,
                'next_step' => 2,
                'prev_step' => null,
                'plan' => 'scale',
                'description' => 'First, lets get you onboarded. Please schedule a kick-off call and fill in your kick-off form.',
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'name' => 'Setup Analytics',
                'orders' => 4,
                'next_step' => 5,
                'prev_step' => 3,
                'plan' => 'scale',
                'description' => "",
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'name' => 'Setup Your Monstro Account',
                'orders' => 3,
                'next_step' => 4,
                'prev_step' => 2,
                'plan' => 'scale',
                'description' => "Next, we need you to set up your account with all the right business information.",
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'name' => 'Check Out the Courses',
                'orders' => 5,
                'next_step' => null,
                'prev_step' => 4,
                'plan' => 'scale',
                'description' => "",
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'name' => 'Join the Community',
                'orders' => 2,
                'next_step' => 3,
                'prev_step' => 1,
                'plan' => 'scale',
                'description' => "Being around like minded people is what drives results! Join our private community for members only.",
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'name' => 'Claim Your Bonuses',
                'orders' => 3,
                'next_step' => null,
                'prev_step' => 6,
                'plan' => 'trial',
                'description' => "This is how you can claim your bonuses for your 14 day trial of Monstro.",
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'name' => 'Start Here',
                'orders' => 1,
                'next_step' => 8,
                'prev_step' => null,
                'plan' => 'trial',
                'description' => "First, lets get you onboarded. Please schedule a kick-off call.",
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach($progress_steps as $progress_step){
            ProgressStep::create($progress_step);       
        }
    }


}