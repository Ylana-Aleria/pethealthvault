<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        Role::updateOrCreate(
            ['name' => 'Admin'], // Match this column
            ['description' => 'Administrator role'] // Update this if it exists
        );
    }
}
