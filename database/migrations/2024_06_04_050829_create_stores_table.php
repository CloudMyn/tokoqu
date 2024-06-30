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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('code', 6)->unique();
            $table->foreignId('owner_id')->references('id')->on('owners')->cascadeOnDelete();
            $table->integer('assets')->default(0);
            $table->string('address');
            $table->string('coordinate_lat')->nullable();
            $table->string('coordinate_lng')->nullable();
            $table->timestamps();

            $table->unique(['code', 'owner_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
