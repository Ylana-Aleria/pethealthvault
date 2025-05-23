<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'aleanalyn15@gmail.com'], // Unique key
            [
                'firstname' => 'Ylana',
                'lastname' => 'Aleria',
                'password' => Hash::make('alialiali'),
                'role_id' => 1
            ]
        );
    }
}
