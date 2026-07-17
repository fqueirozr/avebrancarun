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
        Schema::table('payment_gateway_settings', function (Blueprint $table) {
            $table->string('pix_bank')->nullable()->after('pix_receiver_city');
            $table->string('pix_agency', 20)->nullable()->after('pix_bank');
            $table->string('pix_account', 30)->nullable()->after('pix_agency');
            $table->string('pix_account_holder')->nullable()->after('pix_account');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_gateway_settings', function (Blueprint $table) {
            $table->dropColumn([
                'pix_bank',
                'pix_agency',
                'pix_account',
                'pix_account_holder',
            ]);
        });
    }
};
