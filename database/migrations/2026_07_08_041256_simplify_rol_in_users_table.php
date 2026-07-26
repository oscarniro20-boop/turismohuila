<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Primero actualizar cualquier rol que no sea admin a turista
        DB::table('users')
            ->whereNotIn('rol', ['admin', 'turista'])
            ->update(['rol' => 'turista']);

        // Luego cambiar la columna
        Schema::table('users', function (Blueprint $table) {
            $table->string('rol')->default('turista')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('rol')->default('turista')->change();
        });
    }
};