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
        Schema::create('mail_settings', function (Blueprint $table) {
            $table->id();
            $table->string('mailer')->default('log');
            $table->string('scheme')->nullable();
            $table->string('host')->default('127.0.0.1');
            $table->unsignedSmallInteger('port')->default(2525);
            $table->string('username')->nullable();
            $table->text('password')->nullable();
            $table->string('from_address')->default('hello@example.com');
            $table->string('from_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_settings');
    }
};
