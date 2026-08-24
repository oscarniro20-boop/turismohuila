<?php

namespace App\Concerns;

trait HasGalleryImageUrl
{
    public function getUrlAttribute(): string
    {
        $ruta = (string) $this->ruta;

        return str_starts_with($ruta, 'http')
            ? $ruta
            : asset(ltrim($ruta, '/'));
    }
}
