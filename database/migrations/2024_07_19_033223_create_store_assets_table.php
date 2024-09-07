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
        Schema::create('store_assets', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->string('message', 244);
            $table->integer('amount');
            $table->enum('type', ['in', 'out', 'hold']);
            $table->string('store_code');
            $table->timestamps();

            $table->foreign('store_code')->on('stores')->references('code')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_assets');
    }
};
