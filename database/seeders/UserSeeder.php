<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@turismo-huila.com',
            'password' => Hash::make('password123'),
            'rol'      => 'admin',
        ]);

        User::create([
            'name'     => 'Oscar Nicolas',
            'email'    => 'oscar@turismo-huila.com',
            'password' => Hash::make('password123'),
            'rol'      => 'turista',
        ]);
    }
}
