<?php

namespace Database\Factories;

use App\Models\Lugar;
use Illuminate\Database\Eloquent\Factories\Factory;

class LugarFactory extends Factory
{
    protected $model = Lugar::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'nombre' => $this->faker->unique()->city(),
            'municipio' => $this->faker->randomElement(['Neiva', 'Garzón', 'Pitalito', 'San Agustín', 'La Plata']),
            'categoria' => $this->faker->randomElement(['Naturaleza', 'Cultura', 'Aventura']),
            'descripcion' => $this->faker->paragraph(),
            'latitud' => $this->faker->latitude(),
            'longitud' => $this->faker->longitude(),
        ];
    }
}