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
            $table->string('payment_gateway')->nullable()->after('payment_status');
            $table->string('payment_gateway_reference')->nullable()->after('payment_gateway');
            $table->text('payment_checkout_url')->nullable()->after('payment_gateway_reference');
            $table->string('pix_receipt_path')->nullable()->after('payment_checkout_url');
            $table->timestamp('pix_receipt_submitted_at')->nullable()->after('pix_receipt_path');

            $table->index(['payment_gateway', 'payment_gateway_reference']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shirt_orders', function (Blueprint $table) {
            $table->dropIndex(['payment_gateway', 'payment_gateway_reference']);
            $table->dropColumn([
                'payment_gateway',
                'payment_gateway_reference',
                'payment_checkout_url',
                'pix_receipt_path',
                'pix_receipt_submitted_at',
            ]);
        });
    }
};
