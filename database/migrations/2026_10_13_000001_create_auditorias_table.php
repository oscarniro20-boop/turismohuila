<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla central de auditoría (Fase 6): traza quién hizo qué y cuándo.
 *
 * Una sola tabla para todas las operaciones administrativas (usuarios,
 * importaciones CSV, y en el futuro CRUD de módulos). La fila se conserva
 * aunque el usuario se elimine (user_id nullable + nullOnDelete).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('accion', 80);      // ej: 'usuario.creado', 'importacion.csv'
            $table->string('entidad', 60)->nullable(); // ej: 'usuario', 'importacion'
            $table->unsignedBigInteger('entidad_id')->nullable();
            $table->json('datos')->nullable();  // resumen estructurado (contadores, cambios)
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();

            $table->index('accion');
            $table->index(['entidad', 'entidad_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditorias');
    }
};