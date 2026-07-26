<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class GaleriaImagenService
{
    /** @param array<int, UploadedFile> $imagenes */
    public function guardar(Model $entidad, array $imagenes, string $directorio): void
    {
        $orden = (int) $entidad->imagenes()->max('orden');

        foreach ($imagenes as $imagen) {
            $ruta = Storage::disk('public')->putFile($directorio, $imagen);

            $entidad->imagenes()->create([
                'ruta' => $ruta,
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
        $ruta = ltrim((string) $imagen->ruta, '/');

        if (Storage::disk('public')->exists($ruta)) {
            Storage::disk('public')->delete($ruta);
        } elseif (File::exists(public_path($ruta))) {
            File::delete(public_path($ruta));
        }

        $imagen->delete();
    }
}
