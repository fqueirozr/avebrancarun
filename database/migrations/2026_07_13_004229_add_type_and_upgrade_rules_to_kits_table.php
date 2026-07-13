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
        Schema::table('kits', function (Blueprint $table) {
            $table->string('type')->default('standard')->after('price')->index();
            $table->text('rules')->nullable()->after('type');
            $table->unsignedInteger('upgrade_1_referrals')->nullable()->after('rules');
            $table->unsignedInteger('upgrade_2_referrals')->nullable()->after('upgrade_1_referrals');
            $table->unsignedInteger('upgrade_3_referrals')->nullable()->after('upgrade_2_referrals');
        });

        DB::table('kits')->where('is_half_registration', true)->update(['type' => 'pcd_60']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kits', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropColumn(['type', 'rules', 'upgrade_1_referrals', 'upgrade_2_referrals', 'upgrade_3_referrals']);
        });
    }
};
