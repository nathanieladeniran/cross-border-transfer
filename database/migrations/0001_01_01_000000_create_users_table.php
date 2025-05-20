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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->morphs('profile'); #Add the proper columns for a polymorphic table;
            $table->mediumInteger('email_otp')->nullable()->comment('otp password sent to mail');
            $table->mediumInteger('phone_otp')->nullable()->comment('one time password sent to mobile number');
            $table->string('email')->unique();
            $table->string('temp_email')->nullable();
            $table->string('password');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('removed_at')->nullable();
            $table->string('referrer')->nullable()->comment('referrer\'s code');
            $table->string('referral_token')->unique()->nullable()->comment('personal referal token');
            $table->string('token')->nullable();
            $table->timestamp('email_otp_expires_at')->nullable();
            $table->timestamp('email_otp_verified_at')->nullable();
            $table->timestamp('phone_otp_expires_at')->nullable();
            $table->timestamp('phone_otp_verified_at')->nullable();
            $table->enum('account_status', ['active','suspended','banned', 'deactivated'])->default('active');
            $table->timestamp('banned_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->timestamp('deactivated')->nullable();
            $table->json('shufti_response')->nullable();
            $table->timestamp('shufti_profile_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('deactivation_request')->nullable();
            $table->float('risk_score')->default(0.0);
            $table->string('risk_type')->default('LOW_RISK');
            $table->json('risk_metas')->nullable();
            $table->json('monoova_payid')->nullable();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
