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
        Schema::create('event_settings', function (Blueprint $table) {
            $table->id();
            $table->string('event_date')->nullable();
            $table->string('event_location')->nullable();
            $table->dateTime('registration_deadline')->nullable();
            $table->unsignedInteger('max_registrations')->nullable();
            $table->string('organizer_legal_name')->nullable();
            $table->string('organizer_cnpj', 18)->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_whatsapp')->nullable();
            $table->longText('general_information')->nullable();
            $table->text('kit_information')->nullable();
            $table->longText('baggage_storage_information')->nullable();
            $table->longText('timing_information')->nullable();
            $table->longText('special_registrations_information')->nullable();
            $table->longText('course_information')->nullable();
            $table->json('course_images')->nullable();
            $table->longText('regulation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_settings');
    }
};
