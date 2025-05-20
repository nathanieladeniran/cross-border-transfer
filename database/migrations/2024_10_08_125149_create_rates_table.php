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
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('from_country_id')->unsigned();
            $table->bigInteger('to_country_id')->unsigned();
            $table->integer('fee_rule_key')->default(1);
            $table->decimal('buy',10,4)->nullable();
            $table->decimal('sell',10,4)->nullable();
            $table->decimal('fee',10,4)->nullable();
            $table->decimal('additional_rate',10,4)->nullable();
            $table->decimal('minimum',10,4)->default(0);
            $table->decimal('maximum',10,4)->default(0);
            $table->decimal('promorate',10,4)->default(0);
            $table->decimal('times',10,4)->default(0);
            $table->decimal('walletpercent',10,4)->default(0);
            $table->decimal('walletminimum_value',10,4)->default(0);
            $table->decimal('walletminimum_transfer',10,4)->default(0);
            $table->boolean('is_default')->default(false);
            $table->enum('islocal', ['yes', 'no'])->default('no');
            $table->enum('bound_direction', ['outbound', 'inbound'])->default('outbound');
            $table->integer('ratemanager_id')->nullable();
            $table->timestamp('deactivated_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rates');
    }
};
