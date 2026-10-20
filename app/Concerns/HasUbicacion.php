<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Ubicación nacional (departamento + municipio) para entidades turísticas.
 * El municipio es un campo existente; departamento es la nueva dimensión nacional.
 */
trait HasUbicacion
{
    /** Filtra por departamento y/o municipio. */
    public function scopeUbicacion(Builder $q, ?string $departamento, ?string $municipio): Builder
    {
        if (filled($departamento)) {
            $q->where('departamento', $departamento);
        }

        if (filled($municipio)) {
            $q->where('municipio', $municipio);
        }

        return $q;
    }

    /** "Municipio, Departamento"; solo departamento si no hay municipio. */
    public function getUbicacionTextoAttribute(): ?string
    {
        $municipio = trim((string) ($this->municipio ?? ''));
        $departamento = trim((string) ($this->departamento ?? ''));

        if ($municipio !== '' && $departamento !== '') {
            return $municipio . ', ' . $departamento;
        }

        if ($municipio !== '') {
            return $municipio;
        }

        return $departamento !== '' ? $departamento : null;
    }
}
