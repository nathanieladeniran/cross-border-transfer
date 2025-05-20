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
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained();
            $table->smallInteger('country_id');
            $table->string('first_name',150);
            $table->string('last_name',150);
            $table->string('email',50)->nullable();
            $table->string('phone_no',75);
            $table->string('address')->nullable();
            $table->string('suburb')->nullable();
            $table->string('idtype_details')->nullable();
            $table->integer('idtype_id')->nullable();
            $table->string('comment')->nullable();
            $table->integer('kaasi_receiver_accepted')->nullable();
            $table->json('kaasi_metas')->nullable();
            $table->string('alt_phone_no')->nullable();
            $table->integer('kaasi_receiver_id')->nullable();
            $table->string('kaasi_status')->nullable();
            $table->enum('bound_direction', ['outbound', 'inbound'])->default('outbound');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
