<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class GaleriaImagenService
{
    /** @param array<int, UploadedFile> $imagenes */
    public function guardar(Model $entidad, array $imagenes, string $directorio): void
    {
        $orden = (int) $entidad->imagenes()->max('orden');
        $destino = public_path($directorio);

        if (!is_dir($destino)) {
            mkdir($destino, 0755, true);
        }

        foreach ($imagenes as $imagen) {
            $nombre = time() . '_' . uniqid() . '.' . $imagen->getClientOriginalExtension();
            $imagen->move($destino, $nombre);

            $entidad->imagenes()->create([
                'ruta' => $directorio . '/' . $nombre,
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

        if (File::exists(public_path($ruta))) {
            File::delete(public_path($ruta));
        }

        $imagen->delete();
    }
}
