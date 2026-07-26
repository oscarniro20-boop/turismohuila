<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_imagenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hoteles')->onDelete('cascade');
            $table->string('ruta');
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_imagenes');
    }
};