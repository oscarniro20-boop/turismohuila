<?php

namespace App\Http\Controllers;

use App\Services\GaleriaImagenService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class Controller
{
    protected function reglasGaleria(): array
    {
        return [
            'imagenes' => ['nullable', 'array'],
            'imagenes.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    protected function guardarImagenesGaleria(Model $entidad, Request $request, string $directorio): void
    {
        if ($request->hasFile('imagenes')) {
            app(GaleriaImagenService::class)->guardar($entidad, $request->file('imagenes'), $directorio);
        }
    }

    protected function eliminarImagenesGaleriaSeleccionadas(Model $entidad, Request $request): int
    {
        return app(GaleriaImagenService::class)->eliminarSeleccionadas(
            $entidad,
            array_filter((array) $request->input('eliminar_imagenes', []))
        );
    }
}
