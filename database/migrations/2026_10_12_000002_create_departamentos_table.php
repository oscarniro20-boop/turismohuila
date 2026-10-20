<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('departamentos')) {
            return;
        }

        Schema::create('departamentos', function (Blueprint $table) {
            $table->string('cod_dpto', 5)->primary();
            $table->string('nombre', 80);
            $table->index('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departamentos');
    }
};
