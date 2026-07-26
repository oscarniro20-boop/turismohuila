<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agencia_tour', function (Blueprint $table) {
            $table->decimal('precio', 10, 2)->nullable()->after('tour_id');
        });
    }

    public function down(): void
    {
        Schema::table('agencia_tour', function (Blueprint $table) {
            $table->dropColumn('precio');
        });
    }
};