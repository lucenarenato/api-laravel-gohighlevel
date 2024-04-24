<?php

namespace Database\Seeders;

use App\Models\Action;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Action::create(['name' => Action::NO_OF_CLASSES]);
        Action::create(['name' => Action::LEVEL_ACHIEVED]);
        Action::create(['name' => Action::No_OF_REFERRALS]);
    }
}
