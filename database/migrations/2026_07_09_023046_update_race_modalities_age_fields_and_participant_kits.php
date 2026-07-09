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
            $table->unsignedSmallInteger('age_start')->nullable()->after('type');
            $table->unsignedSmallInteger('age_end')->nullable()->after('age_start');
            $table->dropColumn(['age_range', 'price']);
        });

        Schema::table('participant_registrations', function (Blueprint $table) {
            $table->foreignId('kit_id')
                ->nullable()
                ->after('race_modality_id')
                ->constrained()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participant_registrations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kit_id');
        });

        Schema::table('race_modalities', function (Blueprint $table) {
            $table->string('age_range')->nullable()->after('type');
            $table->decimal('price', 10, 2)->nullable()->after('google_maps_embed_url');
            $table->dropColumn(['age_start', 'age_end']);
        });
    }
};
