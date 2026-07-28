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
        Schema::table('shirt_orders', function (Blueprint $table) {
            $table->string('customer_cpf', 11)->nullable()->after('customer_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shirt_orders', function (Blueprint $table) {
            $table->dropColumn('customer_cpf');
        });
    }
};
