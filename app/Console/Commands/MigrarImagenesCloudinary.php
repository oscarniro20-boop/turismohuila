<?php

namespace App\Console\Commands;

use App\Models\Agencia;
use App\Models\Guia;
use App\Models\Hotel;
use App\Models\Lugar;
use App\Models\Restaurante;
use App\Models\Tour;
use App\Models\User;
use App\Services\CloudinaryImageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Fase 3: migra las imágenes principales existentes en disco local
 * (public/imagenes y public/portadas) a Cloudinary.
 *
 * - NO borra nada de public/imagenes ni public/portadas.
 * - Omite registros cuya columna ya es URL de Cloudinary (idempotente).
 * - Verifica cada URL subida con HTTP HEAD antes de actualizar la BD.
 * - Deja bitácora en storage/app/migracion-cloudinary.csv.
 *
 * Simulación (por defecto):  php artisan migrar:imagenes-cloudinary
 * Ejecución real:            php artisan migrar:imagenes-cloudinary --ejecutar
 */
class MigrarImagenesCloudinary extends Command
{
    protected $signature = 'migrar:imagenes-cloudinary
                            {--ejecutar : Aplica los cambios reales (sin esta opción solo simula)}
                            {--modelo= : Limita a: lugares,hoteles,restaurantes,tours,guias,agencias,users}';

    protected $description = 'Migra imágenes principales locales a Cloudinary (sube, verifica y actualiza la columna; no borra locales)';

    private array $totales = ['migradas' => 0, 'omitidas_ya_cloudinary' => 0, 'sin_archivo_local' => 0, 'errores' => 0];

    public function handle(CloudinaryImageService $servicio): int
    {
        $ejecutar = (bool) $this->option('ejecutar');
        $filtro = $this->option('modelo');

        if (! config('services.cloudinary.url')) {
            $this->error('CLOUDINARY_URL no está configurada. Abortando.');

            return 1;
        }

        $mapa = [
            'lugares'       => [Lugar::class, 'imagen'],
            'hoteles'       => [Hotel::class, 'imagen'],
            'restaurantes'  => [Restaurante::class, 'imagen'],
            'tours'         => [Tour::class, 'imagen'],
            'guias'         => [Guia::class, 'foto'],
            'agencias'      => [Agencia::class, 'imagen'],
            'users'         => [User::class, 'foto_portada'],
        ];

        if ($filtro && ! isset($mapa[$filtro])) {
            $this->error("Modelo desconocido: {$filtro}. Opciones: ".implode(', ', array_keys($mapa)));

            return 1;
        }

        $bitacora = fopen(storage_path('app/migracion-cloudinary.csv'), $ejecutar ? 'a' : 'w');
        fputcsv($bitacora, ['tabla', 'id', 'ruta_anterior', 'accion', 'url_nueva_o_error']);

        foreach ($mapa as $nombre => [$modelo, $campo]) {
            if ($filtro && $nombre !== $filtro) {
                continue;
            }

            $this->line('');
            $this->info("=== {$nombre} (campo: {$campo}) ===");

            $modelo::query()
                ->whereNotNull($campo)
                ->where($campo, '!=', '')
                ->orderBy('id')
                ->chunkById(50, function ($registros) use ($nombre, $campo, $servicio, $ejecutar, $bitacora) {
                    foreach ($registros as $registro) {
                        $this->procesar($registro, $nombre, $campo, $servicio, $ejecutar, $bitacora);
                    }
                });
        }

        fclose($bitacora);
        $this->resumen($ejecutar);

        return $this->totales['errores'] > 0 ? 1 : 0;
    }

    private function procesar($registro, string $nombre, string $campo, CloudinaryImageService $servicio, bool $ejecutar, $bitacora): void
    {
        $ruta = (string) $registro->{$campo};

        // Ya migrado: no tocar (idempotente).
        if ($servicio->esUrlCloudinary($ruta)) {
            $this->totales['omitidas_ya_cloudinary']++;

            return;
        }

        // Ruta local relativa, ej. imagenes/lugares/foto.jpg o portadas/x.jpg
        $absoluta = public_path($ruta);

        if (! File::exists($absoluta)) {
            $this->totales['sin_archivo_local']++;
            $this->warn("  [{$nombre} #{$registro->id}] archivo local inexistente: {$ruta}");
            fputcsv($bitacora, [$nombre, $registro->id, $ruta, 'SIN_ARCHIVO', '']);

            return;
        }

        // Carpeta destino: sin el prefijo local imagenes/ (el servicio ya lo quita);
        // portadas va aparte.
        $carpeta = str_starts_with($ruta, 'portadas/') ? 'portadas' : dirname($ruta);

        try {
            if (! $ejecutar) {
                $this->line("  [DRY-RUN] {$nombre} #{$registro->id}: subir '{$ruta}' a '{$carpeta}'");
                fputcsv($bitacora, [$nombre, $registro->id, $ruta, 'DRY_RUN', '']);
                $this->totales['migradas']++;

                return;
            }

            $url = $servicio->subir($absoluta, $carpeta);

            // Verificación: la URL debe responder 200 antes de tocar la BD.
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_NOBODY => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 15,
            ]);
            curl_exec($ch);
            $http = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            curl_close($ch);

            if ($http !== 200) {
                throw new \RuntimeException("La URL subida respondió HTTP {$http}, no actualizo la BD.");
            }

            $registro->update([$campo => $url]);
            $this->totales['migradas']++;
            $this->info("  ✓ {$nombre} #{$registro->id}: {$url}");
            fputcsv($bitacora, [$nombre, $registro->id, $ruta, 'MIGRADA', $url]);
        } catch (\Throwable $e) {
            $this->totales['errores']++;
            $this->error("  ✗ {$nombre} #{$registro->id}: ".$e->getMessage());
            fputcsv($bitacora, [$nombre, $registro->id, $ruta, 'ERROR', $e->getMessage()]);
        }
    }

    private function resumen(bool $ejecutar): void
    {
        $this->line('');
        $this->info('═══ RESUMEN '.($ejecutar ? '(EJECUTADO)' : '(SIMULACIÓN — usa --ejecutar para aplicar)').' ═══');
        foreach ($this->totales as $k => $v) {
            $this->line("  {$k}: {$v}");
        }
        $this->line('  Bitácora: '.storage_path('app/migracion-cloudinary.csv'));
    }
}
