<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use App\Services\CloudinaryImageService;

class GaleriaImagenService
{
    /** @param array<int, UploadedFile> $imagenes */
    public function guardar(Model $entidad, array $imagenes, string $directorio): void
    {
        $orden = (int) $entidad->imagenes()->max('orden');
        $servicio = app(CloudinaryImageService::class);

        foreach ($imagenes as $imagen) {
            $secureUrl = $servicio->subir($imagen->getRealPath(), $directorio);

            $entidad->imagenes()->create([
                'ruta' => $secureUrl,
                'orden' => ++$orden,
            ]);
        }
    }

    public function eliminarSeleccionadas(Model $entidad, array $ids): int
    {
        $imagenes = $entidad->imagenes()->whereKey($ids)->get();

        foreach ($imagenes as $imagen) {
            $this->eliminar($imagen);
        }

        return $imagenes->count();
    }

    /** @param Collection<int, Model> $imagenes */
    public function eliminarTodas(Collection $imagenes): void
    {
        $imagenes->each(fn (Model $imagen) => $this->eliminar($imagen));
    }

    public function eliminar(Model $imagen): void
    {
        $servicio = app(CloudinaryImageService::class);
        $ruta = (string) $imagen->ruta;

        if ($servicio->esUrlCloudinary($ruta)) {
            $servicio->eliminarPorUrl($ruta);
        } else {
            $rutaLimpia = ltrim($ruta, '/');

            if (File::exists(public_path($rutaLimpia))) {
                File::delete(public_path($rutaLimpia));
            }
        }

        $imagen->delete();
    }
}
