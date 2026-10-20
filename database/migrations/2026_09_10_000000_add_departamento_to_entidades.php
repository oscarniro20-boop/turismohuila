<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dimensión nacional de ubicación: columna 'departamento' en las entidades
 * turísticas. Aditiva y compatible: los registros actuales quedan en 'Huila'.
 * El municipio existente se conserva tal cual.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Entidades con contenido real actual (todo en Huila por ahora).
        $conDefault = ['lugares', 'hoteles', 'restaurantes', 'tours', 'transportes'];

        foreach ($conDefault as $tabla) {
            Schema::table($tabla, function (Blueprint $table) use ($tabla) {
                if (! Schema::hasColumn($tabla, 'departamento')) {
                    $table->string('departamento', 80)->default('Huila')->index();
                }
            });
        }

        // Entidades donde la ubicación nacional es opcional.
        $nullable = ['agencias', 'guias'];

        foreach ($nullable as $tabla) {
            Schema::table($tabla, function (Blueprint $table) use ($tabla) {
                if (! Schema::hasColumn($tabla, 'departamento')) {
                    $table->string('departamento', 80)->nullable()->index();
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['lugares', 'hoteles', 'restaurantes', 'tours', 'transportes', 'agencias', 'guias'] as $tabla) {
            Schema::table($tabla, function (Blueprint $table) use ($tabla) {
                if (Schema::hasColumn($tabla, 'departamento')) {
                    $table->dropColumn('departamento');
                }
            });
        }
    }
};
