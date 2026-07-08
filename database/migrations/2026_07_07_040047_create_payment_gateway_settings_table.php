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
        Schema::create('payment_gateway_settings', function (Blueprint $table) {
            $table->id();
            $table->string('gateway')->default('asaas');
            $table->boolean('is_enabled')->default(false);
            $table->string('environment')->default('sandbox');
            $table->text('api_key')->nullable();
            $table->unsignedSmallInteger('checkout_minutes_to_expire')->default(60);
            $table->json('billing_types')->nullable();
            $table->json('charge_types')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_settings');
    }
};
