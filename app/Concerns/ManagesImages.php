<?php

namespace App\Concerns;

use App\Models\Favorito;
use App\Services\CloudinaryImageService;
use App\Services\GaleriaImagenService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

trait ManagesImages
{
    public function subirImagenPrincipal($file, $directorio)
    {
        return app(CloudinaryImageService::class)->subir($file->getRealPath(), $directorio);
    }

    public function eliminarImagen($ruta)
    {
        if (!$ruta) {
            return;
        }

        $servicio = app(CloudinaryImageService::class);
        $ruta = (string) $ruta;

        // Si ya es una URL de Cloudinary (migrada), borrar desde Cloudinary.
        if ($servicio->esUrlCloudinary($ruta)) {
            $servicio->eliminarPorUrl($ruta);

            return;
        }

        // Si sigue siendo una ruta local vieja (antes de la migración), borrar del disco.
        $rutaLimpia = ltrim($ruta, '/');

        if (File::exists(public_path($rutaLimpia))) {
            File::delete(public_path($rutaLimpia));
        }
    }

    public function obtenerFavoritosIds($userId, $tipo): array
    {
        if (!$userId) {
            return [];
        }

        return Favorito::where('user_id', $userId)
            ->where('tipo', $tipo)
            ->pluck('referencia_id')
            ->toArray();
    }

    public function eliminarTodasImagenes($imagenes)
    {
        foreach ($imagenes as $imagen) {
            $this->eliminarImagen($imagen->ruta);
        }
    }

    public function guardarImagenesGaleria(Model $entidad, Request $request, string $directorio): void
    {
        if ($request->hasFile('imagenes')) {
            app(GaleriaImagenService::class)->guardar($entidad, $request->file('imagenes'), $directorio);
        }
    }
}