<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Agrega columna descrip­tiva a `departamentos` y la columna `departamento_id`
     * nullable a las 7 tablas de contenido turístico, con backfill a Huila.
     *
     * ADITIVA y no destructiva: no elimina columnas existentes.
     * La constraint de FK física solo se crea en MySQL; en SQLite (tests) se omite
     * porque no soporta ALTER TABLE ... ADD CONSTRAINT.
     */
    public function up(): void
    {
        $isMySQL = DB::connection()->getDriverName() === 'mysql';

        // ── 1. Enriquecer tabla departamentos ──────────────────────────────
        Schema::table('departamentos', function (Blueprint $table) {
            if (! Schema::hasColumn('departamentos', 'slug')) {
                $table->string('slug', 80)->unique()->nullable()->after('nombre');
            }
            if (! Schema::hasColumn('departamentos', 'region')) {
                $table->string('region', 30)->nullable()->after('slug');
            }
            if (! Schema::hasColumn('departamentos', 'capital')) {
                $table->string('capital', 80)->nullable()->after('region');
            }
            if (! Schema::hasColumn('departamentos', 'destacado')) {
                $table->boolean('destacado')->default(false)->after('capital');
            }
            if (! Schema::hasColumn('departamentos', 'orden')) {
                $table->integer('orden')->nullable()->after('destacado');
            }
            if (! Schema::hasColumn('departamentos', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('orden');
            }
            if (! Schema::hasColumn('departamentos', 'imagen')) {
                $table->string('imagen', 255)->nullable()->after('descripcion');
            }
            if (! Schema::hasColumn('departamentos', 'latitud')) {
                $table->decimal('latitud', 9, 6)->nullable()->after('imagen');
            }
            if (! Schema::hasColumn('departamentos', 'longitud')) {
                $table->decimal('longitud', 9, 6)->nullable()->after('latitud');
            }
        });

        // ── 2. Agregar departamento_id a las 7 tablas ─────────────────────
        $tablas = ['lugares', 'hoteles', 'restaurantes', 'tours', 'agencias', 'guias', 'transportes'];

        foreach ($tablas as $tabla) {
            if (! Schema::hasTable($tabla)) {
                continue;
            }

            Schema::table($tabla, function (Blueprint $table) use ($tabla) {
                if (! Schema::hasColumn($tabla, 'departamento_id')) {
                    $table->string('departamento_id', 5)
                        ->nullable()
                        ->index()
                        ->after('id');
                }
            });

            // FK hacia departamentos(cod_dpto) solo en MySQL
            if ($isMySQL) {
                DB::statement(sprintf(
                    'ALTER TABLE `%s` ADD CONSTRAINT `fk_%s_departamento_id` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`cod_dpto`) ON DELETE SET NULL',
                    $tabla,
                    $tabla
                ));
            }
        }

        // ── 3. Backfill: asignar Huila a registros existentes ─────────────
        $codDptoHuila = DB::table('departamentos')
            ->where('nombre', 'Huila')
            ->value('cod_dpto');

        if ($codDptoHuila) {
            foreach ($tablas as $tabla) {
                if (! Schema::hasTable($tabla)) {
                    continue;
                }

                DB::table($tabla)
                    ->whereNull('departamento_id')
                    ->where('departamento', 'Huila')
                    ->update(['departamento_id' => $codDptoHuila]);
            }
        }
    }

    public function down(): void
    {
        $isMySQL = DB::connection()->getDriverName() === 'mysql';

        $tablas = ['lugares', 'hoteles', 'restaurantes', 'tours', 'agencias', 'guias', 'transportes'];

        foreach ($tablas as $tabla) {
            if (! Schema::hasTable($tabla)) {
                continue;
            }

            // Eliminar FK si existe (solo MySQL)
            if ($isMySQL) {
                DB::statement(sprintf(
                    'ALTER TABLE `%s` DROP FOREIGN KEY `fk_%s_departamento_id`',
                    $tabla,
                    $tabla
                ));
            }

            if (Schema::hasColumn($tabla, 'departamento_id')) {
                Schema::table($tabla, function (Blueprint $table) use ($tabla) {
                    $table->dropColumn('departamento_id');
                });
            }
        }

        // Quitar columnas agregadas a departamentos (mantener las originales)
        Schema::table('departamentos', function (Blueprint $table) {
            foreach (['slug', 'region', 'capital', 'destacado', 'orden', 'descripcion', 'imagen', 'latitud', 'longitud'] as $col) {
                if (Schema::hasColumn('departamentos', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};