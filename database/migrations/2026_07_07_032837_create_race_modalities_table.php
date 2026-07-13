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
        Schema::create('race_modalities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->unsignedSmallInteger('age_start')->nullable();
            $table->unsignedSmallInteger('age_end')->nullable();
            $table->string('distance')->nullable();
            $table->date('race_date')->nullable();
            $table->time('race_time')->nullable();
            $table->string('google_maps_embed_url', 2048)->nullable();
            $table->longText('course_information')->nullable();
            $table->json('course_images')->nullable();
            $table->unsignedInteger('max_participants')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('race_modalities');
    }
};
