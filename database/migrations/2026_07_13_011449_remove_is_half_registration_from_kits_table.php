<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('kits')
            ->where('is_half_registration', true)
            ->update(['type' => 'pcd_60']);

        Schema::table('kits', function (Blueprint $table) {
            $table->dropColumn('is_half_registration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kits', function (Blueprint $table) {
            $table->boolean('is_half_registration')->default(false)->after('price');
        });

        DB::table('kits')
            ->where('type', 'pcd_60')
            ->update(['is_half_registration' => true]);
    }
};
