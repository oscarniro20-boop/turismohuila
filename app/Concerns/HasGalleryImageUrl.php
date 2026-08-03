<?php

namespace App\Concerns;

trait HasGalleryImageUrl
{
    public function getUrlAttribute(): string
    {
        return asset(ltrim((string) $this->ruta, '/'));
    }
}
