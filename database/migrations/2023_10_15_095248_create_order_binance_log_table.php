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
        Schema::create('order_binance_log', function (Blueprint $table) {
            $table->id();
            $table->string('dt_ins');
            $table->string('ts_ins');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->enum('stock', ['binance_spot', 'binance_futures']);
            $table->enum('action', ['create', 'cancel', 'get', 'websocket']);
            $table->json('request');
            $table->json('data');
            $table->integer('stock_order_id_1');
            $table->integer('stock_order_id_2');
            $table->tinyInteger('state')->default(0);
            $table->string('weight_ip');
            $table->string('weight_uid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_binance_log');
    }
};
