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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->char('iso3');
            $table->char('iso2');
            $table->string('phonecode')->nullable();
            $table->string('capital')->nullable();
            $table->string('currency')->nullable();
            $table->string('native')->nullable();
            $table->string('emoji')->nullable();
            $table->string('emojiU')->nullable();
            $table->tinyInteger('flag')->nullable();
            $table->string('wikiDataId')->nullable();
            $table->tinyInteger('left')->nullable();
            $table->tinyInteger('right')->nullable();
            $table->string('currencylogo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
