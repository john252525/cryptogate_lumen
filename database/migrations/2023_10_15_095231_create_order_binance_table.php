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
        Schema::create('order_binance', function (Blueprint $table) {
            $table->id();
            $table->dateTime('dt_ins');
            $table->timestamp('ts_ins');
            $table->foreignId('preorder_id')->references('id')->on('preorders');
            $table->foreignId('stock_id')->references('id')->on('stocks');
            $table->json('data');
            $table->integer('stock_order_id_1');
            $table->integer('stock_order_id_2');
            $table->enum('state', ['created', 'canceled', 'filled']);
            $table->dateTime('dt_upd');
            $table->timestamp('ts_upd');
            $table->dateTime('dt_check');
            $table->timestamp('ts_check');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_binance');
    }
};
