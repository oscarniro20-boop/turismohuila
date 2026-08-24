<?php

namespace App\Utilities;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Auditoría de hardening.
 *
 * Limpia fotos de portada huérfanas (archivos que ya no existen físicamente)
 * sobre cualquier modelo que almacene la ruta en la columna `foto_portada`.
 */
class FotoPortada
{
    /**
     * Recorre todos los registros del modelo indicado y, si la foto de portada
     * almacenada no existe físicamente, la limpia (la pone a null) para evitar
     * referencias rotas.
     *
     * @param  string  $modelClass  Clase del modelo (ej. App\Models\User::class)
     */
    public static function validar(string $modelClass): void
    {
        if (! class_exists($modelClass)) {
            return;
        }

        $columna = 'foto_portada';

        if (! in_array($columna, (new $modelClass)->getFillable(), true)
            && ! in_array($columna, (new $modelClass)->getCasts()['foto_portada'] ?? [], true)
        ) {
            // El modelo no expone la columna foto_portada; nada que validar.
            if (! \Schema::hasColumn((new $modelClass)->getTable(), $columna)) {
                return;
            }
        }

        $modelClass::query()
            ->whereNotNull($columna)
            ->where($columna, '!=', '')
            ->chunkById(100, function ($modelos) use ($columna) {
                foreach ($modelos as $modelo) {
                    $ruta = $modelo->{$columna};
                    if (! self::existeArchivo($ruta)) {
                        $modelo->{$columna} = null;
                        $modelo->save();
                    }
                }
            });
    }

    /**
     * Elimina el archivo físico de la foto de portada del modelo indicado
     * (si existe) y limpia el campo en la base de datos.
     *
     * @param  object  $modelo  Instancia del modelo con foto_portada
     */
    public static function limpiar(object $modelo): void
    {
        $columna = 'foto_portada';
        $ruta = $modelo->{$columna} ?? null;

        if ($ruta) {
            self::eliminarArchivo($ruta);
            $modelo->{$columna} = null;
            $modelo->save();
        }
    }

    /**
     * Comprueba si un archivo referenciado por una ruta relativa existe.
     */
    protected static function existeArchivo(?string $ruta): bool
    {
        if (! $ruta) {
            return false;
        }

        // Soporte legacy: prefijo storage://
        if (str_starts_with($ruta, 'storage://')) {
            return Storage::disk('public')->exists(substr($ruta, 9));
        }

        // Ruta física relativa al directorio public
        $path = public_path($ruta);
        if (File::exists($path)) {
            return true;
        }

        // Ruta dentro del storage/app/public
        if (Storage::disk('public')->exists($ruta)) {
            return true;
        }

        return false;
    }

    /**
     * Elimina un archivo físico si existe.
     */
    protected static function eliminarArchivo(string $ruta): void
    {
        if (str_starts_with($ruta, 'storage://')) {
            Storage::disk('public')->delete(substr($ruta, 9));
            return;
        }

        $path = public_path($ruta);
        if (File::exists($path)) {
            File::delete($path);
            return;
        }

        Storage::disk('public')->delete($ruta);
    }
}
