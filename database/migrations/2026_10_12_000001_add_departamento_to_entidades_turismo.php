<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Añade la columna `departamento` a las entidades turísticas para operar a
 * escala nacional. Migración ADITIVA y no destructiva:
 *  - lugares, hoteles, restaurantes, tours, transportes: NOT NULL, default 'Huila'
 *    (los datos actuales quedan correctamente en Huila).
 *  - agencias, guias: nullable (aún no migradas).
 * No elimina columnas ni convierte campos existentes.
 */
return new class extends Migration
{
    /** @var array<string, bool> tabla => notNull */
    private array $tablas = [
        'lugares'      => true,
        'hoteles'      => true,
        'restaurantes' => true,
        'tours'        => true,
        'transportes'  => true,
        'agencias'     => false,
        'guias'        => false,
    ];

    public function up(): void
    {
        foreach ($this->tablas as $tabla => $notNull) {
            if (! Schema::hasTable($tabla) || Schema::hasColumn($tabla, 'departamento')) {
                continue;
            }

            Schema::table($tabla, function (Blueprint $table) use ($notNull) {
                $col = $table->string('departamento', 80);
                if ($notNull) {
                    $col->default('Huila');
                } else {
                    $col->nullable();
                }
                $col->index();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tablas as $tabla => $_) {
            if (Schema::hasTable($tabla) && Schema::hasColumn($tabla, 'departamento')) {
                Schema::table($tabla, function (Blueprint $table) {
                    $table->dropIndex(['departamento']);
                    $table->dropColumn('departamento');
                });
            }
        }
    }
};
