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
        Schema::create('participant_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('athlete_name');
            $table->date('birth_date');
            $table->string('guardian_name')->nullable();
            $table->string('phone');
            $table->string('email');
            $table->string('modality');
            $table->text('notes')->nullable();
            $table->string('payment_status')->default('pending');
            $table->timestamps();

            $table->index(['modality', 'payment_status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_registrations');
    }
};
