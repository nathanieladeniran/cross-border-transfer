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
        Schema::table('idtypes', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('slug');
            $table->string('issuer')->nullable();
            $table->string('type')->nullable();
            $table->string('others')->nullable();
            $table->string('state')->nullable();
            $table->foreignId('country_id')->default(14)->constrained(); // Assuming you have a countries table
            $table->boolean('card_no_required')->default(0);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('idtypes', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('card_no_required');
            $table->dropForeign(['country_id']);
            $table->dropColumn('country_id');
            $table->dropColumn('state');
            $table->dropColumn('others');
            $table->dropColumn('type');
            $table->dropColumn('issuer');
            $table->dropColumn('slug');
            $table->dropColumn('name');
        });
    }
};
