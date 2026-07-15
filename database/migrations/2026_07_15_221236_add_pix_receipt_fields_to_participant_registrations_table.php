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
        Schema::table('participant_registrations', function (Blueprint $table) {
            $table->string('pix_receipt_path')->nullable()->after('payment_checkout_url');
            $table->timestamp('pix_receipt_submitted_at')->nullable()->after('pix_receipt_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participant_registrations', function (Blueprint $table) {
            $table->dropColumn(['pix_receipt_path', 'pix_receipt_submitted_at']);
        });
    }
};
