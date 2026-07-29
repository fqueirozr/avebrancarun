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
        Schema::table('kits', function (Blueprint $table) {
            $table->decimal('size_2xl_surcharge', 10, 2)->default(0)->after('price');
            $table->decimal('size_3xl_surcharge', 10, 2)->default(0)->after('size_2xl_surcharge');
        });

        Schema::table('shirts', function (Blueprint $table) {
            $table->decimal('size_2xl_surcharge', 10, 2)->default(0)->after('registration_price');
            $table->decimal('size_3xl_surcharge', 10, 2)->default(0)->after('size_2xl_surcharge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kits', function (Blueprint $table) {
            $table->dropColumn(['size_2xl_surcharge', 'size_3xl_surcharge']);
        });

        Schema::table('shirts', function (Blueprint $table) {
            $table->dropColumn(['size_2xl_surcharge', 'size_3xl_surcharge']);
        });
    }
};
