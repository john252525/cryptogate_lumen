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
            $table->dateTime('dt_ins');
            $table->integer('ts_ins');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->integer('deal_id')->default(-1);
            $table->foreignId('stock_id')->references('id')->on('stocks');
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
