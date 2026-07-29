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
            $table->timestamp('payment_reminder_sent_at')->nullable();
        });

        Schema::table('shirt_orders', function (Blueprint $table) {
            $table->timestamp('payment_reminder_sent_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participant_registrations', function (Blueprint $table) {
            $table->dropColumn('payment_reminder_sent_at');
        });

        Schema::table('shirt_orders', function (Blueprint $table) {
            $table->dropColumn('payment_reminder_sent_at');
        });
    }
};
