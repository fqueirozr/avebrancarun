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
        Schema::table('race_modalities', function (Blueprint $table) {
            $table->date('race_date')->nullable()->after('distance');
            $table->time('race_time')->nullable()->after('race_date');
            $table->longText('course_information')->nullable()->after('google_maps_embed_url');
            $table->json('course_images')->nullable()->after('course_information');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('race_modalities', function (Blueprint $table) {
            $table->dropColumn([
                'race_date',
                'race_time',
                'course_information',
                'course_images',
            ]);
        });
    }
};
