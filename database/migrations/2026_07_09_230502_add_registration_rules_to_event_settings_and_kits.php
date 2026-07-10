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
        Schema::table('event_settings', function (Blueprint $table) {
            $table->dateTime('registration_deadline')->nullable()->after('event_location');
            $table->unsignedInteger('max_registrations')->nullable()->after('registration_deadline');
        });

        Schema::table('participant_registrations', function (Blueprint $table) {
            $table->string('registration_identity', 11)->nullable()->unique()->after('participant_cpf');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participant_registrations', function (Blueprint $table) {
            $table->dropColumn('registration_identity');
        });

        Schema::table('event_settings', function (Blueprint $table) {
            $table->dropColumn(['registration_deadline', 'max_registrations']);
        });
    }
};
