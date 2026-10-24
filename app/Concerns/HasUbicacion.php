<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

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

    /**
     * Hook saving: deriva departamento_id del nombre del departamento usando el
     * catálogo DIVIPOLA de Colombia. Ignora mayúsculas y tildes. Sin coincidencia,
     * deja departamento_id en null y reporta (sin lanzar excepción).
     */
    public static function bootHasUbicacion(): void
    {
        static::saving(function ($modelo) {
            if (! filled($modelo->departamento)) {
                return;
            }

            $mapa = config('colombia.municipios', []);
            $deptoNormalizado = self::normalizarTexto($modelo->departamento);

            $encontrado = null;
            foreach ($mapa as $clave => $codigos) {
                [$depEnMapa] = explode('|', $clave, 2);
                if (self::normalizarTexto($depEnMapa) === $deptoNormalizado) {
                    $encontrado = $codigos['cod_dpto'] ?? null;
                    break;
                }
            }

            // Si departamento cambió (es sucio), siempre reasignar departamento_id.
            if ($modelo->isDirty('departamento')) {
                $modelo->departamento_id = $encontrado;
                if ($encontrado === null) {
                    logger()->warning("HasUbicacion: no se encontró código para departamento '{$modelo->departamento}'.");
                }
                return;
            }

            // Si departamento no cambió pero departamento_id está null, asignar.
            if (! filled($modelo->departamento_id)) {
                $modelo->departamento_id = $encontrado;
                if ($encontrado === null) {
                    logger()->warning("HasUbicacion: no se encontró código para departamento '{$modelo->departamento}'.");
                }
            }
        });
    }

    private static function normalizarTexto(string $texto): string
    {
        return Str::lower(Str::ascii(trim($texto)));
    }
}
