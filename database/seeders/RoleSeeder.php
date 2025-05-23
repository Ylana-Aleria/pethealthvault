<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        Role::create([
            'name' => 'Admin',
            'description' => 'Administrator role',
        ]);

        Role::create([
            'name' => 'Owner',
            'description' => 'Owner role',
        ]);
    }
}
