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
            if (! Schema::hasColumn('participant_registrations', 'payment_gateway')) {
                $table->string('payment_gateway')->nullable()->after('payment_status');
            }

            if (! Schema::hasColumn('participant_registrations', 'payment_gateway_reference')) {
                $table->string('payment_gateway_reference')->nullable()->after('payment_gateway');
            }

            if (! Schema::hasColumn('participant_registrations', 'payment_checkout_url')) {
                $table->string('payment_checkout_url')->nullable()->after('payment_gateway_reference');
            }
        });

        Schema::table('participant_registrations', function (Blueprint $table) {
            $table->index(['payment_gateway', 'payment_gateway_reference'], 'participant_gateway_ref_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participant_registrations', function (Blueprint $table) {
            $table->dropIndex('participant_gateway_ref_idx');
            $table->dropColumn([
                'payment_gateway',
                'payment_gateway_reference',
                'payment_checkout_url',
            ]);
        });
    }
};
