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
            $table->integer('ts_ins');
            $table->foreignId('user_id')->references('id')->on('user');
            $table->foreignId('stock_id')->references('id')->on('stock');
            $table->enum('action', ['create', 'cancel', 'get', 'websocket']);
            $table->json('request')->nullable(true);
            $table->json('data')->nullable(true);
            $table->integer('stock_order_id_1')->nullable(true);
            $table->integer('stock_order_id_2')->nullable(true);
            $table->integer('state')->default(0);
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
