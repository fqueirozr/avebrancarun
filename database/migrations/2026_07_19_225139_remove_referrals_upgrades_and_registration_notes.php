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
        $referralForeignKey = collect(Schema::getForeignKeys('participant_registrations'))
            ->first(fn (array $foreignKey): bool => $foreignKey['columns'] === ['referred_by_pathfinder_id']);

        if ($referralForeignKey !== null) {
            Schema::table('participant_registrations', function (Blueprint $table) use ($referralForeignKey): void {
                $table->dropForeign($referralForeignKey['name'] ?? $referralForeignKey['columns']);
            });
        }

        $registrationColumns = collect([
            'referred_by_pathfinder_id',
            'pathfinder_upgrade_level',
            'notes',
            'health_notes',
        ])->filter(fn (string $column): bool => Schema::hasColumn('participant_registrations', $column))->all();

        if ($registrationColumns !== []) {
            Schema::table('participant_registrations', function (Blueprint $table) use ($registrationColumns): void {
                $table->dropColumn($registrationColumns);
            });
        }

        $kitColumns = collect([
            'upgrade_1_referrals',
            'upgrade_1_contents',
            'upgrade_2_referrals',
            'upgrade_2_contents',
            'upgrade_3_referrals',
            'upgrade_3_contents',
        ])->filter(fn (string $column): bool => Schema::hasColumn('kits', $column))->all();

        if ($kitColumns !== []) {
            Schema::table('kits', function (Blueprint $table) use ($kitColumns): void {
                $table->dropColumn($kitColumns);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participant_registrations', function (Blueprint $table) {
            $table->foreignId('referred_by_pathfinder_id')->nullable()->constrained('pathfinders')->nullOnDelete();
            $table->unsignedTinyInteger('pathfinder_upgrade_level')->default(0);
            $table->text('notes')->nullable();
            $table->text('health_notes')->nullable();
        });

        Schema::table('kits', function (Blueprint $table) {
            $table->unsignedInteger('upgrade_1_referrals')->nullable();
            $table->text('upgrade_1_contents')->nullable();
            $table->unsignedInteger('upgrade_2_referrals')->nullable();
            $table->text('upgrade_2_contents')->nullable();
            $table->unsignedInteger('upgrade_3_referrals')->nullable();
            $table->text('upgrade_3_contents')->nullable();
        });
    }
};
