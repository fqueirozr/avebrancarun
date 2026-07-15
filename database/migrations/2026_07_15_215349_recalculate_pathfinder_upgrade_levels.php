<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('participant_registrations')
            ->whereNotNull('pathfinder_id')
            ->orderBy('id')
            ->chunkById(100, function ($registrations): void {
                foreach ($registrations as $registration) {
                    $kit = DB::table('kits')->find($registration->kit_id);
                    $referrals = DB::table('participant_registrations')
                        ->where('referred_by_pathfinder_id', $registration->pathfinder_id)
                        ->count();

                    $upgradeLevel = $kit?->type === 'pathfinder'
                        ? collect([
                            $kit->upgrade_1_referrals,
                            $kit->upgrade_2_referrals,
                            $kit->upgrade_3_referrals,
                        ])->filter(fn ($threshold): bool => $threshold !== null && $referrals >= (int) $threshold)->count()
                        : 0;

                    DB::table('participant_registrations')
                        ->where('id', $registration->id)
                        ->update(['pathfinder_upgrade_level' => $upgradeLevel]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
