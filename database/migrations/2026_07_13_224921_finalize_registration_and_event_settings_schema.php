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
        if (! Schema::hasColumn('participant_registrations', 'shirt_size')) {
            Schema::table('participant_registrations', function (Blueprint $table) {
                $table->string('shirt_size', 3)->nullable()->after('athlete_name');
            });
        }

        if (Schema::hasColumn('event_settings', 'start_groups_information')) {
            Schema::table('event_settings', function (Blueprint $table) {
                $table->dropColumn('start_groups_information');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('participant_registrations', 'shirt_size')) {
            Schema::table('participant_registrations', function (Blueprint $table) {
                $table->dropColumn('shirt_size');
            });
        }

        if (! Schema::hasColumn('event_settings', 'start_groups_information')) {
            Schema::table('event_settings', function (Blueprint $table) {
                $table->longText('start_groups_information')->nullable()->after('baggage_storage_information');
            });
        }
    }
};
