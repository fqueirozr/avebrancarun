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
            $table->string('billing_name')->nullable()->after('billing_document');
            $table->string('billing_address')->nullable()->after('billing_name');
            $table->string('billing_address_number', 20)->nullable()->after('billing_address');
            $table->string('billing_province')->nullable()->after('billing_address_number');
            $table->string('billing_postal_code', 8)->nullable()->after('billing_province');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participant_registrations', function (Blueprint $table) {
            $table->dropColumn([
                'billing_name',
                'billing_address',
                'billing_address_number',
                'billing_province',
                'billing_postal_code',
            ]);
        });
    }
};
