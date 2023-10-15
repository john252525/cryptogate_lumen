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
        Schema::create('preorders', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->string('dt_ins');
            $table->string('ts_ins');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('deal_id')->references('id')->on('deals');
            $table->enum('stock', ['binance_spot', 'binance_futures']);
            $table->enum('type', ['market', 'limit', 'oco']);
            $table->enum('side', ['buy', 'sell']);
            $table->enum('positionSide', ['long', 'short']);
            $table->string('pair');
            $table->json('data');
            $table->enum('state', ['new', 'pending', 'created', 'canceled', 'filled']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preorders');
    }
};
