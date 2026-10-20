<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (!app()->environment('local')) {
            return;
        }

        // forceCreate porque 'rol' no es mass-assignable (Fase 5).
        User::forceCreate([
            'name'     => 'Administrador',
            'email'    => 'admin@turismo-huila.com',
            'password' => Hash::make(env('SEED_PASSWORD', 'password123')),
            'rol'      => 'admin',
        ]);

        User::forceCreate([
            'name'     => 'Oscar Nicolas',
            'email'    => 'oscar@turismo-huila.com',
            'password' => Hash::make(env('SEED_PASSWORD', 'password123')),
            'rol'      => 'turista',
        ]);
    }
}
