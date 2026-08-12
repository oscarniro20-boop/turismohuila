<?php

namespace App\Concerns;

trait HasGaleriaRules
{
    /**
     * Obtiene las reglas de validación para imágenes de galería.
     *
     * @return array
     */
    protected function reglasGaleria(): array
    {
        return [
            'imagenes' => 'nullable|array',
            'imagenes.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}