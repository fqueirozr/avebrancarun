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
        Schema::table('kits', function (Blueprint $table) {
            $table->text('upgrade_1_contents')->nullable()->after('upgrade_1_referrals');
            $table->text('upgrade_2_contents')->nullable()->after('upgrade_2_referrals');
            $table->text('upgrade_3_contents')->nullable()->after('upgrade_3_referrals');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kits', function (Blueprint $table) {
            $table->dropColumn([
                'upgrade_1_contents',
                'upgrade_2_contents',
                'upgrade_3_contents',
            ]);
        });
    }
};
