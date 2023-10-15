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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->dateTime('dt_ins');
            $table->timestamp('ts_ins');
            $table->foreignId('preorder_id')->references('id')->on('preorders');
            $table->enum('action', ['create', 'cancel', 'get']);
            $table->enum('mode', ['sync', 'async']);
            $table->tinyInteger('state')->default(0);
            $table->dateTime('dt_upd');
            $table->timestamp('ts_upd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
