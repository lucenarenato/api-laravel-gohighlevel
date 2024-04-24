<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        Role::create(['name' => \App\Models\User::ADMIN]);
        Role::create(['name' => \App\Models\User::VENDOR]);
        Role::create(['name' => \App\Models\User::MEMBER]);
    }
}
