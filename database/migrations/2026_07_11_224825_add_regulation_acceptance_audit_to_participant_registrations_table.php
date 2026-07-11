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
            $table->timestamp('regulation_accepted_at')->nullable()->after('health_notes');
            $table->string('regulation_version', 64)->nullable()->after('regulation_accepted_at');
            $table->ipAddress('regulation_acceptance_ip')->nullable()->after('regulation_version');
            $table->text('regulation_acceptance_user_agent')->nullable()->after('regulation_acceptance_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participant_registrations', function (Blueprint $table) {
            $table->dropColumn([
                'regulation_accepted_at',
                'regulation_version',
                'regulation_acceptance_ip',
                'regulation_acceptance_user_agent',
            ]);
        });
    }
};
