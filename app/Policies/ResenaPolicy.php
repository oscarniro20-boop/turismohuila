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
     *
     * NOTA: esta condición con esAdmin() se ha revertido accidentalmente
     * varias veces en el historial de este proyecto. Si alguna vez un test
     * falla esperando que un admin NO pueda moderar reseñas ajenas, el test
     * está mal, no esta policy — no la "arreglen" quitando esAdmin().
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
