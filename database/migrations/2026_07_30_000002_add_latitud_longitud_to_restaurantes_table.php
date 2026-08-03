<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurantes', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurantes', 'latitud')) {
                $table->decimal('latitud', 10, 7)->nullable()->after('municipio');
            }
            if (!Schema::hasColumn('restaurantes', 'longitud')) {
                $table->decimal('longitud', 10, 7)->nullable()->after('latitud');
            }
        });
    }

    public function down(): void
    {
        Schema::table('restaurantes', function (Blueprint $table) {
            $table->dropColumn(['latitud', 'longitud']);
        });
    }
};