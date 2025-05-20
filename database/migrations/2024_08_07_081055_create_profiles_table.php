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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->integer('country_id')->comment('user country, related to countries table');
            $table->integer('state_id')->nullable()->comment('user state, related to state table');
            $table->enum('gender', ['male', 'female'])->nullable()->comment('gender');
            $table->mediumInteger('idtype_id')->nullable()->comment('identification mode (driver licence, passport, etc), related to idtypes table');
            $table->integer('transactions_count')->default(0)->comment('number of transactions user has made since joined');
            $table->tinyInteger('two_factor_enabled')->default(0);
            $table->tinyInteger('kaasi_sender_accepted')->nullable();
            $table->integer('kaasi_customer_id')->nullable();
            $table->string('occupation', 65)->nullable();
            $table->string('member_id', 20);
            $table->string('firstname',30)->nullable();
            $table->string('lastname', 30)->nullable();
            $table->string('othernames', 50)->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('suburb', 50)->nullable();
            $table->string('statem', 50)->nullable();
            $table->string('postcode', 15)->nullable();
            $table->string('mobile_phone', 175)->unique();
            $table->string('temp_phone', 100)->nullable();
            $table->string('home_phone', 100)->nullable();
            $table->string('idnumber', 40)->nullable();
            $table->string('card_number', 40)->nullable();
            $table->string('other_id', 40)->nullable();
            $table->string('card_issuer', 65)->nullable();
            $table->string('scanned_id_front', 65)->nullable();
            $table->string('scanned_id_rear', 65)->nullable();
            $table->string('compliance_reason')->nullable();
            $table->string('unit_no', 50)->nullable();
            $table->string('street_name')->nullable();
            $table->string('street_no',50)->nullable();
            $table->string('kyc_message')->nullable();
            $table->string('kyc_reference_no',15)->nullable();
            $table->string('kaasi_status', 50)->nullable();
            $table->string('profile_photo',65)->nullable();
            $table->integer('two_factor_phone_otp')->nullable();
            $table->double('walletbalance')->default(0);
            $table->double('local_limit_amount')->nullable();
            $table->integer('transaction_days')->default(0);
            $table->integer('transaction_times')->default(0);
            $table->date('dob')->nullable();
            $table->date('id_issue_date')->nullable();
            $table->date('id_expiry_date')->nullable();
            $table->dateTime('black_listed')->nullable();
            $table->json('kaasi_metas')->nullable();
            $table->timestamp('two_factor_phone_otp_expiry')->nullable();
            $table->timestamp('two_factor_auth_verified')->nullable();
            $table->timestamp('kaasi_created_at')->nullable();
            $table->timestamp('kaasi_updated_at')->nullable();
            $table->timestamp('not_compliance_at')->nullable();
            $table->enum('bound_direction', ['out', 'in'])->nullable();
            $table->timestamp('transaction_limit_date')->nullable();
            $table->timestamp('personal_details_at')->nullable();
            $table->timestamp('kyc_details_at')->nullable();
            $table->timestamp('address_details_at')->nullable();
            $table->timestamp('kyc_at')->nullable();
            $table->json('shufti_response')->nullable();
            $table->timestamp('shufti_profile_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('call_shuftipro_at')->nullable();
            $table->json('idtype_metas')->nullable();
            $table->tinyInteger('cosmosec_occupation_industry')->nullable();
            $table->double('estimated_monthly_send')->default(0.0);
            $table->timestamp('face_verified_at')->nullable();
            $table->json('face_verification_metas')->nullable();
            $table->string('occupation_industry')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
