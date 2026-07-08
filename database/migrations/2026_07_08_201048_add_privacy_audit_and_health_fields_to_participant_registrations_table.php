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
            $table->string('emergency_contact_name')->nullable()->after('notes');
            $table->string('emergency_contact_phone', 11)->nullable()->after('emergency_contact_name');
            $table->text('health_notes')->nullable()->after('emergency_contact_phone');
            $table->boolean('promotional_opt_in')->default(false)->after('health_notes');
            $table->timestamp('privacy_policy_accepted_at')->nullable()->after('promotional_opt_in');
            $table->string('privacy_policy_version', 20)->nullable()->after('privacy_policy_accepted_at');
            $table->string('privacy_policy_acceptance_ip', 45)->nullable()->after('privacy_policy_version');
            $table->text('privacy_policy_acceptance_user_agent')->nullable()->after('privacy_policy_acceptance_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participant_registrations', function (Blueprint $table) {
            $table->dropColumn([
                'emergency_contact_name',
                'emergency_contact_phone',
                'health_notes',
                'promotional_opt_in',
                'privacy_policy_accepted_at',
                'privacy_policy_version',
                'privacy_policy_acceptance_ip',
                'privacy_policy_acceptance_user_agent',
            ]);
        });
    }
};
