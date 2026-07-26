<?php

namespace App\Concerns;

trait HasRating
{
    public function getCalificacionPromedioAttribute(): float
    {
        return (float) $this->resenas()->avg('calificacion');
    }

    public function getTotalResenasAttribute(): int
    {
        return $this->resenas()->count();
    }
}