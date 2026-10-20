<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Importa el catálogo geográfico nacional (DIVIPOLA) desde config/colombia.php.
 * Idempotente: usa updateOrCreate por código, transaccional, no duplica registros.
 * NO inventa contenido turístico; solo geografía oficial.
 */
class GeografiaSeeder extends Seeder
{
    public function run(): void
    {
        $config = config('colombia');
        $codigos = $config['municipios'] ?? [];

        DB::transaction(function () use ($codigos) {
            foreach ($codigos as $clave => $par) {
                [$departamento, $municipio] = array_pad(explode('|', $clave, 2), 2, '');

                DB::table('departamentos')->updateOrInsert(
                    ['cod_dpto' => $par['cod_dpto']],
                    ['nombre' => $departamento]
                );

                DB::table('municipios')->updateOrInsert(
                    ['cod_mpio' => $par['cod_mpio']],
                    [
                        'cod_dpto' => $par['cod_dpto'],
                        'nombre' => $municipio,
                    ]
                );
            }
        });

        $this->command?->info('Geografía importada: '.DB::table('departamentos')->count().' departamentos, '
            .DB::table('municipios')->count().' municipios.');
    }
}
