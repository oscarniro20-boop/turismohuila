<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_paquetes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agencia_id')->constrained('agencias')->onDelete('cascade');
            $table->foreignId('tour_id')->constrained('tours')->onDelete('cascade');
            $table->decimal('precio', 10, 2)->nullable();
            $table->integer('duracion_dias')->nullable();
            $table->integer('cupo_maximo')->nullable();
            $table->text('descripcion')->nullable();
            $table->timestamps();
            
            $table->unique(['agencia_id', 'tour_id']);
        });

        // Migrar datos existentes de agencia_tour a tour_paquetes
        DB::table('agencia_tour')->get()->each(function ($fila) {
            DB::table('tour_paquetes')->insert([
                'agencia_id' => $fila->agencia_id,
                'tour_id' => $fila->tour_id,
                'precio' => $fila->precio,
                'duracion_dias' => DB::table('tours')->where('id', $fila->tour_id)->value('duracion_dias'),
                'cupo_maximo' => DB::table('tours')->where('id', $fila->tour_id)->value('cupo_maximo'),
                'descripcion' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_paquetes');
    }
};