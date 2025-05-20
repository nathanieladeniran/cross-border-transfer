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
        Schema::create('pop_messages', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->string('title');
            $table->enum('type', ['warning','info','danger','success'])->default('info');
            $table->enum('active', ['yes','no'])->default('no');
            $table->enum('purpose', ['pop', 'sticky'])->default('pop');
            $table->timestamp('expired_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pop_messages');
    }
};
