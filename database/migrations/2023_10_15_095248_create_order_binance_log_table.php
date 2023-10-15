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
            $table->dateTime('dt_ins');
            $table->timestamp('ts_ins');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('stock_id')->references('id')->on('stocks');
            $table->enum('action', ['create', 'cancel', 'get', 'websocket']);
            $table->json('request');
            $table->json('data');
            $table->integer('stock_order_id_1');
            $table->integer('stock_order_id_2');
            $table->tinyInteger('state')->default(0);
            $table->dateTime('weight_ip');
            $table->timestamp('weight_uid');
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
