<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('participant_registrations', function (Blueprint $table) {
            $table->string('protocol_number', 30)->nullable()->unique()->after('id');
            $table->boolean('filled_by_legal_representative')->default(false)->after('guardian_cpf');
        });

        DB::table('participant_registrations')
            ->select('id')
            ->orderBy('id')
            ->eachById(function (object $registration): void {
                DB::table('participant_registrations')
                    ->where('id', $registration->id)
                    ->update(['protocol_number' => 'AVR-'.Str::ulid()]);
            });

        Schema::table('participant_registrations', function (Blueprint $table) {
            $table->string('protocol_number', 30)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participant_registrations', function (Blueprint $table) {
            $table->dropUnique(['protocol_number']);
            $table->dropColumn(['protocol_number', 'filled_by_legal_representative']);
        });
    }
};
