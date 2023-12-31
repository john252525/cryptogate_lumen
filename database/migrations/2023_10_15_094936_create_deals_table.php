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
        Schema::create('deal', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->dateTime('dt_ins');
            $table->integer('ts_ins');
            $table->foreignId('user_id')->references('id')->on('user');
            $table->integer('count_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
