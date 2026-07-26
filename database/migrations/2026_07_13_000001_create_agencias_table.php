<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agencias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('municipio')->nullable();
            $table->string('telefono_contacto')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agencias');
    }
};