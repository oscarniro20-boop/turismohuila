<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('municipios')) {
            return;
        }

        Schema::create('municipios', function (Blueprint $table) {
            $table->string('cod_mpio', 8)->primary();
            $table->string('cod_dpto', 5);
            $table->string('nombre', 120);
            $table->index(['cod_dpto']);
            $table->foreign('cod_dpto')->references('cod_dpto')->on('departamentos')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('municipios');
    }
};
