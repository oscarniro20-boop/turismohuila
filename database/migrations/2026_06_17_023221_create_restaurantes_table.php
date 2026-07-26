<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurantes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion');
            $table->string('municipio');
            $table->string('tipo_cocina');
            $table->decimal('precio_promedio', 10, 2);
            $table->string('imagen')->nullable();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurantes');
    }
};