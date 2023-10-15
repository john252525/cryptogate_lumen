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
            $table->string('dt_ins');
            $table->string('ts_ins');
            $table->foreignId('preorder_id')->references('id')->on('preorders');
            $table->enum('stock', ['binance_spot', 'binance_futures']);
            $table->json('data');
            $table->integer('stock_order_id_1');
            $table->integer('stock_order_id_2');
            $table->enum('state', ['created', 'canceled', 'filled']);
            $table->string('dt_upd');
            $table->string('ts_upd');
            $table->string('dt_check');
            $table->string('ts_check');
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
