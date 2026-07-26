<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guia_lugar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guia_id')->constrained('guias')->onDelete('cascade');
            $table->foreignId('lugar_id')->constrained('lugares')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['guia_id', 'lugar_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guia_lugar');
    }
};