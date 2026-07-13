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
        Schema::table('participant_registrations', function (Blueprint $table) {
            $table->foreignId('race_modality_id')
                ->nullable()
                ->after('email')
                ->constrained()
                ->nullOnDelete();
            $table->index(
                ['race_modality_id', 'result_status', 'overall_rank'],
                'registrations_race_result_rank_index',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participant_registrations', function (Blueprint $table) {
            $table->dropIndex('registrations_race_result_rank_index');
            $table->dropConstrainedForeignId('race_modality_id');
        });
    }
};
