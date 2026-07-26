<?php

namespace App\Concerns;

use App\Services\GaleriaImagenService;

trait HasGalleryImages
{
    public static function bootHasGalleryImages(): void
    {
        static::deleting(function ($modelo): void {
            app(GaleriaImagenService::class)->eliminarTodas($modelo->imagenes()->get());
        });
    }
}
