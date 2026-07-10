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
        if (! Schema::hasColumn('participant_registrations', 'bib_number')) {
            Schema::table('participant_registrations', fn (Blueprint $table) => $table->string('bib_number')->nullable()->after('modality'));
        }

        if (! Schema::hasColumn('participant_registrations', 'result_status')) {
            Schema::table('participant_registrations', fn (Blueprint $table) => $table->string('result_status')->default('awaiting')->after('bib_number'));
        }

        if (! Schema::hasColumn('participant_registrations', 'elapsed_time')) {
            Schema::table('participant_registrations', fn (Blueprint $table) => $table->time('elapsed_time')->nullable()->after('result_status'));
        }

        if (! Schema::hasColumn('participant_registrations', 'result_category')) {
            Schema::table('participant_registrations', fn (Blueprint $table) => $table->string('result_category')->nullable()->after('elapsed_time'));
        }

        if (! Schema::hasColumn('participant_registrations', 'overall_rank')) {
            Schema::table('participant_registrations', fn (Blueprint $table) => $table->unsignedInteger('overall_rank')->nullable()->after('result_category'));
        }

        if (! Schema::hasColumn('participant_registrations', 'sex_rank')) {
            Schema::table('participant_registrations', fn (Blueprint $table) => $table->unsignedInteger('sex_rank')->nullable()->after('overall_rank'));
        }

        if (! Schema::hasColumn('participant_registrations', 'category_rank')) {
            Schema::table('participant_registrations', fn (Blueprint $table) => $table->unsignedInteger('category_rank')->nullable()->after('sex_rank'));
        }

        Schema::table('participant_registrations', function (Blueprint $table) {
            $table->index(
                ['race_modality_id', 'result_status', 'overall_rank'],
                'registrations_race_result_rank_index'
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
            $table->dropColumn(['bib_number', 'result_status', 'elapsed_time', 'result_category', 'overall_rank', 'sex_rank', 'category_rank']);
        });
    }
};
