<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pathfinders', function (Blueprint $table) {
            $table->string('cpf', 11)->nullable()->unique()->after('name');
            $table->dropUnique(['code']);
            $table->dropColumn('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pathfinders', function (Blueprint $table) {
            $table->char('code', 4)->nullable()->unique()->after('name');
            $table->dropUnique(['cpf']);
            $table->dropColumn('cpf');
        });
    }
};
