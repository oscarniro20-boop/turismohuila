<?php

namespace App\Concerns;

use Illuminate\Support\Facades\Storage;

trait HasGalleryImageUrl
{
    public function getUrlAttribute(): string
    {
        $ruta = ltrim((string) $this->ruta, '/');

        return Storage::disk('public')->exists($ruta)
            ? Storage::disk('public')->url($ruta)
            : asset($ruta);
    }
}
