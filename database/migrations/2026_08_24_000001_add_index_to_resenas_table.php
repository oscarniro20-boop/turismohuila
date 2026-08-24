<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Índice para las consultas más frecuentes del sistema de reseñas:
     * filtrar por (tipo, referencia_id) en detalles públicos y borrados en cascada manual.
     */
    public function up(): void
    {
        Schema::table('resenas', function (Blueprint $table) {
            $table->index(['tipo', 'referencia_id']);
        });
    }

    public function down(): void
    {
        Schema::table('resenas', function (Blueprint $table) {
            $table->dropIndex(['tipo', 'referencia_id']);
        });
    }
};
