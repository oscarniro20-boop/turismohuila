<?php

namespace App\Models;

use App\Concerns\HasUrlImagen;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUrlImagen;

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
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

    protected function columnaImagen(): string
    {
        return 'foto_portada';
    }
}