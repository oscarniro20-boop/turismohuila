<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\Lugar;
use App\Models\Hotel;
use App\Models\Restaurante;
use App\Models\Tour;
use App\Models\Guia;
use App\Models\Agencia;

/**
 * SOLO LECTURA + genera reporte CSV. No modifica BD ni seeder.
 * php artisan seeder:proponer-mapeo
 */
class ProponerMapeoSeeder extends Command
{
    protected $signature = 'seeder:proponer-mapeo';
    protected $description = 'Propone (solo propone, no aplica) que imagen recuperada corresponde a cada entidad';

    public function handle(): int
    {
        $csvEntrada = storage_path('app/reporte-recuperacion-imagenes.csv');
        $csvSalida  = storage_path('app/mapeo-propuesto-seeder.csv');

        if (! File::exists($csvEntrada)) {
            $this->error("No existe {$csvEntrada}");

            return self::FAILURE;
        }

        // Carpeta -> [modelo, campoNombre]. Guia usa 'nombre'; su imagen va en 'foto'.
        $modelos = [
            'imagenes/lugares'      => [Lugar::class,       'nombre'],
            'imagenes/hoteles'      => [Hotel::class,       'nombre'],
            'imagenes/restaurantes' => [Restaurante::class, 'nombre'],
            'imagenes/tours'        => [Tour::class,        'nombre'],
            'imagenes/guias'        => [Guia::class,        'nombre'],
            'imagenes/agencias'     => [Agencia::class,     'nombre'],
            'portadas'              => [null, null], // portadas de user: sin match por nombre
        ];

        $filas = array_map('str_getcsv', file($csvEntrada));
        $cabecera = array_shift($filas);
        // sanear BOM UTF-8 que queda pegado a la primera columna
        $cabecera = array_map(fn ($c) => preg_replace('/^\x{FEFF}/u', '', trim((string) $c)), $cabecera);
        $idx = array_flip($cabecera);

        $out = fopen($csvSalida, 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($out, ['archivo_original', 'destino_csv', 'tipo_detectado', 'entidad_id', 'entidad_nombre', 'confianza']);

        $total = $automaticos = $sinMatch = 0;

        foreach ($filas as $fila) {
            if (count($fila) < 3) {
                continue;
            }

            $carpeta = $fila[$idx['carpeta_original']];
            $archivo = $fila[$idx['nombre_archivo_original']];
            $url     = $fila[$idx['url_cloudinary']];
            $total++;

            [$clase, $campoNombre] = $modelos[$carpeta] ?? [null, null];

            // Portadas: no hay nombre de entidad que casar
            if ($clase === null) {
                fputcsv($out, [$archivo, $url, 'user_portada', '', '', 'sin_match']);
                $sinMatch++;
                continue;
            }

            $textoArchivo = $this->normalizar($this->parteHumana($archivo));

            if ($textoArchivo === '') {
                fputcsv($out, [$archivo, $url, class_basename($clase), '', '', 'sin_match']);
                $sinMatch++;
                continue;
            }

            $candidatas = [];
            foreach ($clase::all(['id', 'nombre']) as $entidad) {
                $textoEntidad = $this->normalizar((string) $entidad->nombre);
                if ($textoEntidad === '') {
                    continue;
                }
                $contiene = str_contains($textoEntidad, $textoArchivo) || str_contains($textoArchivo, $textoEntidad);
                $min = min(mb_strlen($textoEntidad), mb_strlen($textoArchivo));
                $score = similar_text($textoEntidad, $textoArchivo);
                $porcentaje = $min > 0 ? ($score / $min) * 100 : 0;

                if ($contiene || $porcentaje >= 70) {
                    $candidatas[] = ['id' => $entidad->id, 'nombre' => $entidad->nombre, 'score' => $porcentaje];
                }
            }

            if (count($candidatas) === 1) {
                $c = $candidatas[0];
                fputcsv($out, [$archivo, $url, class_basename($clase), $c['id'], $c['nombre'], 'automatico']);
                $automaticos++;
            } elseif (count($candidatas) > 1) {
                $nombres = implode(' | ', array_column($candidatas, 'nombre'));
                fputcsv($out, [$archivo, $url, class_basename($clase) . " (ambiguo: {$nombres})", '', '', 'sin_match']);
                $sinMatch++;
            } else {
                fputcsv($out, [$archivo, $url, class_basename($clase), '', '', 'sin_match']);
                $sinMatch++;
            }
        }

        fclose($out);

        $this->info("Filas procesadas: {$total}");
        $this->info("Match automatico: {$automaticos}");
        $this->info("Sin match / ambiguo: {$sinMatch}");
        $this->line("Reporte: {$csvSalida}");
        $this->warn('Nada fue modificado: ni base de datos, ni seeder, ni imagenes.');

        return self::SUCCESS;
    }

    /**
     * Quita timestamp inicial y extension; guiones bajos -> espacios.
     */
    private function parteHumana(string $archivo): string
    {
        $sinExt = pathinfo($archivo, PATHINFO_FILENAME);
        $sinTs = preg_replace('/^\d+_/', '', $sinExt);
        $sinTs = preg_replace('/^0_/', '', (string) $sinTs);

        return str_replace('_', ' ', (string) $sinTs);
    }

    private function normalizar(string $s): string
    {
        $s = strtolower(Str::ascii(trim($s)));
        // quitar sufijos numericos de version ("san agustin2" -> "san agustin")
        $s = preg_replace('/\d+$/', '', trim($s));

        return trim((string) $s);
    }
}

