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
            $table->timestamp('special_kit_rules_accepted_at')->nullable()->after('data_confirmation_acceptance_user_agent');
            $table->string('special_kit_rules_version')->nullable()->after('special_kit_rules_accepted_at');
            $table->ipAddress('special_kit_rules_acceptance_ip')->nullable()->after('special_kit_rules_version');
            $table->text('special_kit_rules_acceptance_user_agent')->nullable()->after('special_kit_rules_acceptance_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participant_registrations', function (Blueprint $table) {
            $table->dropColumn([
                'special_kit_rules_accepted_at',
                'special_kit_rules_version',
                'special_kit_rules_acceptance_ip',
                'special_kit_rules_acceptance_user_agent',
            ]);
        });
    }
};
