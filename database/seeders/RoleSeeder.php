<?php

use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        Role::firstOrCreate(
            ['name' => 'Admin'],
            ['description' => 'Administrator role']
        );

        Role::firstOrCreate(
            ['name' => 'Owner'],
            ['description' => 'Pet owner role']
        );
    }
}
