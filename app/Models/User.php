<?php

namespace App\Models;

use App\Concerns\HasUrlImagen;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUrlImagen;

    protected $fillable = [
        'name',
        'email',
        'password',
        // 'rol' NO es mass-assignable (Fase 5): evita la escalada de privilegios
        // por User::create($request->all()). Se asigna solo de forma explícita
        // (forceFill/forceCreate en AdminUserController, factory y seeders).
        'foto_portada',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            // 'activo' NO es mass-assignable: se cambia solo desde la acción
            // administrativa dedicada (AdminUserController::estado).
            'activo' => 'boolean',
        ];
    }

    public function esAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    public function esTurista(): bool
    {
        return $this->rol === 'turista';
    }

    /** ¿Tiene la cuenta acceso activo al sitio? */
    public function estaActivo(): bool
    {
        return (bool) $this->activo;
    }

    /** Reseñas del usuario (se eliminan en cascada por FK). */
    public function resenas(): HasMany
    {
        return $this->hasMany(Resena::class);
    }

    protected function columnaImagen(): string
    {
        return 'foto_portada';
    }
}