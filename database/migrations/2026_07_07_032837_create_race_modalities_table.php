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
            $table->string('age_range')->nullable();
            $table->string('distance')->nullable();
            $table->decimal('price', 10, 2)->nullable();
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
