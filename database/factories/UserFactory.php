<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            // Las factories corren en modo unguarded: el valor coincide con el
            // default de BD y evita que $user->rol quede null en memoria.
            'rol' => 'turista',
            // Igual motivo que 'rol': evita $user->activos null en memoria.
            'activo' => true,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an administrator.
     *
     * 'rol' no es mass-assignable en User, por lo que el state array no puede
     * asignarlo: se fuerza directamente en el modelo tras crearlo.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [])
            ->afterCreating(function (User $user): void {
                if ($user->rol !== 'admin') {
                    $user->forceFill(['rol' => 'admin'])->save();
                }
            });
    }
}
