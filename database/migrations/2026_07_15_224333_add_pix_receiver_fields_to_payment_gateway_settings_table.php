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
            $table->string('pix_receiver_name', 25)->nullable()->after('pix_key');
            $table->string('pix_receiver_city', 15)->nullable()->after('pix_receiver_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_gateway_settings', function (Blueprint $table) {
            $table->dropColumn(['pix_receiver_name', 'pix_receiver_city']);
        });
    }
};
