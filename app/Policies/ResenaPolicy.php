<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Resena;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResenaPolicy
{
    use HandlesAuthorization;

    /**
     * Determina si el usuario puede actualizar la reseña.
     */
    public function update(User $user, Resena $resena): bool
    {
        return $user->esAdmin() || $user->id === $resena->user_id;
    }

    /**
     * Determina si el usuario puede eliminar la reseña.
     */
    public function delete(User $user, Resena $resena): bool
    {
        return $user->esAdmin() || $user->id === $resena->user_id;
    }
}