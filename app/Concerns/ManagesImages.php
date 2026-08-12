<?php

namespace App\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait ManagesImages
{
    /**
     * Sube una imagen principal a la carpeta especificada.
     */
    protected function subirImagenPrincipal(UploadedFile $file, string $carpeta): ?string
    {
        $nombre = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $destino = public_path("imagenes/{$carpeta}");

        if (!is_dir($destino)) {
            mkdir($destino, 0755, true);
        }

        $file->move($destino, $nombre);

        return "imagenes/{$carpeta}/{$nombre}";
    }

    /**
     * Elimina una imagen del filesystem.
     */
    protected function eliminarImagen(?string $ruta): void
    {
        if (!$ruta) {
            return;
        }

        $rutaCompleta = public_path($ruta);

        if (File::exists($rutaCompleta)) {
            File::delete($rutaCompleta);
        }
    }

    /**
     * Obtiene los IDs de favoritos de un usuario para un tipo específico.
     */
    protected function obtenerFavoritosIds(?int $userId, string $tipo): array
    {
        if (!$userId) {
            return [];
        }

        return \App\Models\Favorito::where('user_id', $userId)
            ->where('tipo', $tipo)
            ->pluck('referencia_id')
            ->toArray();
    }

    /**
     * Elimina todas las imágenes de una colección.
     */
    protected function eliminarTodasImagenes(Collection $imagenes): void
    {
        $imagenes->each(function (Model $imagen) {
            $this->eliminarImagen($imagen->ruta ?? null);
            $imagen->delete();
        });
    }

    /**
     * Guarda imágenes de galería usando el servicio especializado.
     */
    protected function guardarImagenesGaleria(Model $entidad, $request, string $directorio): void
    {
        if (!$request->hasFile('imagenes')) {
            return;
        }

        $service = new \App\Services\GaleriaImagenService();
        $service->guardar($entidad, $request->file('imagenes'), $directorio);
    }
}
