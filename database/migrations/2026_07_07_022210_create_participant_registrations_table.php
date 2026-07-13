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
        Schema::create('participant_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('protocol_number', 30)->unique();
            $table->string('athlete_name');
            $table->string('shirt_size', 3);
            $table->date('birth_date');
            $table->string('sex', 10)->nullable();
            $table->string('participant_cpf', 11)->nullable();
            $table->string('registration_identity', 11)->nullable()->unique();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_cpf', 11)->nullable();
            $table->boolean('filled_by_legal_representative')->default(false);
            $table->string('phone');
            $table->string('email');
            $table->string('billing_document', 14)->nullable();
            $table->string('billing_name')->nullable();
            $table->string('billing_address')->nullable();
            $table->string('billing_address_number', 20)->nullable();
            $table->string('billing_province')->nullable();
            $table->string('billing_postal_code', 8)->nullable();
            $table->string('modality');
            $table->string('bib_number')->nullable();
            $table->string('result_status')->default('awaiting');
            $table->time('elapsed_time')->nullable();
            $table->string('result_category')->nullable();
            $table->unsignedInteger('overall_rank')->nullable();
            $table->unsignedInteger('sex_rank')->nullable();
            $table->unsignedInteger('category_rank')->nullable();
            $table->text('notes')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 11)->nullable();
            $table->text('health_notes')->nullable();
            $table->timestamp('regulation_accepted_at')->nullable();
            $table->string('regulation_version', 64)->nullable();
            $table->ipAddress('regulation_acceptance_ip')->nullable();
            $table->text('regulation_acceptance_user_agent')->nullable();
            $table->timestamp('privacy_policy_accepted_at')->nullable();
            $table->string('privacy_policy_version', 20)->nullable();
            $table->ipAddress('privacy_policy_acceptance_ip')->nullable();
            $table->text('privacy_policy_acceptance_user_agent')->nullable();
            $table->timestamp('data_confirmation_accepted_at')->nullable();
            $table->ipAddress('data_confirmation_acceptance_ip')->nullable();
            $table->text('data_confirmation_acceptance_user_agent')->nullable();
            $table->timestamp('special_kit_rules_accepted_at')->nullable();
            $table->string('special_kit_rules_version')->nullable();
            $table->ipAddress('special_kit_rules_acceptance_ip')->nullable();
            $table->text('special_kit_rules_acceptance_user_agent')->nullable();
            $table->string('payment_status')->default('pending');
            $table->string('payment_gateway')->nullable();
            $table->string('payment_gateway_reference')->nullable();
            $table->string('payment_checkout_url')->nullable();
            $table->timestamps();

            $table->index(['modality', 'payment_status']);
            $table->index(['payment_gateway', 'payment_gateway_reference'], 'participant_gateway_ref_idx');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_registrations');
    }
};
