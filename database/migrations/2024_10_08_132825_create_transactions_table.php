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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->integer('user_id')->unsigned();
            $table->integer('from_country_id')->unsigned();
            $table->integer('to_country_id')->unsigned();
            $table->integer('location_id')->unsigned()->nullable();
            $table->integer('account_id')->unsigned();
            $table->integer('payout_id')->unsigned()->nullable();
            $table->integer('to_country_currency')->nullable();
            $table->integer('payin_id')->nullable()->comment('Payin Identification');
            $table->string('payin');
            $table->string('reference');
            $table->string('send_amount',20);
            $table->string('received_amount',20);
            $table->string('pickup_type')->nullable(); //Not neeeded
            $table->string('source_of_fund')->nullable(); //Not neeeded
            $table->text('comment')->nullable();
            $table->decimal('rate',10,4);
            $table->decimal('tax')->default(0.0);
            $table->decimal('commission')->default(0.0);
            $table->decimal('promotions')->default(0.0);
            $table->decimal('minimum')->default(0.0);
            $table->decimal('walletvalue')->default(0.0);
            $table->json('meta')->nullable();
            $table->json('kaasi_metas')->nullable();
            $table->json('payin_payload')->nullable()->comment('Formerly called parameters');
            $table->enum('status', ['successful', 'cancelled', 'pending', 'failed', 'reject', 'refund', 'suspended'])->default('pending');
            $table->datetime('status_at')->nullable();
            $table->enum('payin_status', ['successful', 'cancelled', 'pending', 'failed'])->nullable()->default('pending');
            $table->datetime('payin_status_at')->nullable();
            $table->enum('payout_status', ['successful', 'cancelled', 'pending', 'failed', 'inprogress'])->nullable()->default('pending');
            $table->datetime('payout_status_at')->nullable();
            $table->datetime('verify_bank_transfer')->nullable();
            $table->datetime('is_logged')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->enum('bound_direction', ['outbound', 'inbound'])->default('outbound');
            $table->float('risk_score')->default(0.0);
            $table->string('risk_type')->default('LOW_RISK');
            $table->json('risk_metas')->nullable();
            $table->morphs('transactionwith');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
