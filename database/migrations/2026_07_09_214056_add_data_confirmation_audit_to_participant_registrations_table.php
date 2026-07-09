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
            $table->timestamp('data_confirmation_accepted_at')->nullable()->after('privacy_policy_acceptance_user_agent');
            $table->string('data_confirmation_acceptance_ip', 45)->nullable()->after('data_confirmation_accepted_at');
            $table->text('data_confirmation_acceptance_user_agent')->nullable()->after('data_confirmation_acceptance_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participant_registrations', function (Blueprint $table) {
            $table->dropColumn([
                'data_confirmation_accepted_at',
                'data_confirmation_acceptance_ip',
                'data_confirmation_acceptance_user_agent',
            ]);
        });
    }
};
