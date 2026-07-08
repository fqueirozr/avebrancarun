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
            $table->longText('general_information')->nullable()->after('contact_whatsapp');
            $table->longText('baggage_storage_information')->nullable()->after('kit_information');
            $table->longText('start_groups_information')->nullable()->after('baggage_storage_information');
            $table->longText('timing_information')->nullable()->after('start_groups_information');
            $table->longText('special_registrations_information')->nullable()->after('timing_information');
            $table->longText('course_information')->nullable()->after('special_registrations_information');
            $table->json('course_images')->nullable()->after('course_information');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_settings', function (Blueprint $table) {
            $table->dropColumn([
                'general_information',
                'baggage_storage_information',
                'start_groups_information',
                'timing_information',
                'special_registrations_information',
                'course_information',
                'course_images',
            ]);
        });
    }
};
