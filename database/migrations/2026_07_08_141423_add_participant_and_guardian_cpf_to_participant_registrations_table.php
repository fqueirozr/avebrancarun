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
            $table->string('participant_cpf', 11)->nullable()->after('birth_date');
            $table->string('guardian_cpf', 11)->nullable()->after('guardian_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participant_registrations', function (Blueprint $table) {
            $table->dropColumn(['participant_cpf', 'guardian_cpf']);
        });
    }
};
