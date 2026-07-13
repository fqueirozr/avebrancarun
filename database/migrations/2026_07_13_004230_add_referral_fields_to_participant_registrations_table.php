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
            $table->foreignId('pathfinder_id')->nullable()->after('kit_id')->constrained()->nullOnDelete()->unique();
            $table->foreignId('referred_by_pathfinder_id')->nullable()->after('pathfinder_id')->constrained('pathfinders')->nullOnDelete()->index();
            $table->unsignedTinyInteger('pathfinder_upgrade_level')->default(0)->after('referred_by_pathfinder_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participant_registrations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('referred_by_pathfinder_id');
            $table->dropConstrainedForeignId('pathfinder_id');
            $table->dropColumn('pathfinder_upgrade_level');
        });
    }
};
