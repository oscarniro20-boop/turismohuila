<?php

namespace App\Concerns;

trait HasUrlImagen
{
    /**
     * Resuelve la URL pública de la imagen del modelo.
     *
     * Si el campo ya es una URL (por ej. la de Cloudinary tras la migración),
     * se devuelve tal cual; si sigue siendo una ruta local relativa (antes de la
     * migración), se envuelve con asset().
     */
    public function getUrlImagenAttribute(): ?string
    {
        $valor = $this->{$this->columnaImagen()} ?? null;

        if (! is_string($valor) || $valor === '') {
            return null;
        }

        return str_starts_with($valor, 'http')
            ? $valor
            : asset(ltrim($valor, '/'));
    }

    protected function columnaImagen(): string
    {
        return 'imagen';
    }
}