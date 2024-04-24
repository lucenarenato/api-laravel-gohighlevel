<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Program;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = [
            [
                'custom_field_ghl_id' => '9We8u8uhiJKlLe38knjew34q98ue5r9ty',
                'name' => 'Program 1',
                'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                'capacity' => 20,
                'min_age' => 11,
                'max_age' => 60,
                'avatar' => null,
            ],
            [
                'custom_field_ghl_id' => 'Aer8u8uhiJKMNop38knjew34q98ue5r9ty',
                'name' => 'Program 2',
                'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                'capacity' => 18,
                'min_age' => 16,
                'max_age' => 48,
                'avatar' => null,
            ],
            [
                'custom_field_ghl_id' => 'Aer8u8uhiJKMNop38knjew34q98ue5r9ty',
                'name' => 'Program 3',
                'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                'capacity' => 25,
                'min_age' => 22,
                'max_age' => 42,
                'avatar' => null,
            ],
            [
                'custom_field_ghl_id' => 'Aer8u8uhiJKMNop38knjew34q98ue5r9ty',
                'name' => 'Program 3',
                'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
                'capacity' => 100,
                'min_age' => 3,
                'max_age' => 80,
                'avatar' => null,
            ],
        ];

        $locations = Location::take(3)->get();

        foreach ($locations as $index => $location) {
            if (isset($programs[$index])) {
                $program = $programs[$index];
        
                $newProgram = new Program([
                    // 'custom_field_ghl_id' => $program['custom_field_ghl_id'],
                    'name' => $program['name'],
                    'description' => $program['description'],
                    'capacity' => $program['capacity'],
                    'min_age' => $program['min_age'],
                    'max_age' => $program['max_age'],
                    'avatar' => $program['avatar']
                ]);
        
                $location->programs()->save($newProgram);
            }
        }
    }
}
