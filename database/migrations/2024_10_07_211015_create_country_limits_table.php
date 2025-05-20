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
        Schema::create('country_limits', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('country_id')->unsigned();
            $table->boolean('status')->default(0);
            $table->float('amount');
            $table->integer('days');
            $table->integer('times');
            $table->float('yearly_amount')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_limits');
    }
};
