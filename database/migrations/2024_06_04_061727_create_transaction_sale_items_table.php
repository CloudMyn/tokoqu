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
        Schema::create('transaction_sale_items', function (Blueprint $table) {
            $table->id();
            $table->string('product_sku');
            $table->string('product_name');
            $table->integer('total_qty');
            $table->integer('sale_price');
            $table->integer('sale_profit');
            $table->string('store_code');
            $table->foreignId('product_id')->nullable();
            $table->foreignId('transaction_id')->references('id')->on('transaction_sales')->cascadeOnDelete();
            $table->timestamps();

            $table->foreign('product_id')->on('products')->references('id')->onDelete('set null');
            $table->foreign('store_code')->on('stores')->references('code')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_sale_items');
    }
};
