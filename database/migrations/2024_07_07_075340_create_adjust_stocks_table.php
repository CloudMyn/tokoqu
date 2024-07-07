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
        Schema::create('adjust_stocks', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->decimal('total_amount', 15, 2);
            $table->integer('total_qty');
            $table->enum('type', ['plus', 'minus']);
            $table->string('admin_name');
            $table->foreignId('admin_id')->nullable();
            $table->foreignId('product_id');
            $table->string('store_code');
            $table->timestamps();


            $table->foreign('product_id')->on('products')->references('id')->onDelete('cascade');
            $table->foreign('admin_id')->on('users')->references('id')->onDelete('set null');
            $table->foreign('store_code')->on('stores')->references('code')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adjust_stocks');
    }
};
