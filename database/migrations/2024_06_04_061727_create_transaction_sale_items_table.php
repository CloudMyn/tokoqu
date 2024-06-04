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
            $table->decimal('product_cost', 15, 2);
            $table->decimal('sale_price', 15, 2);
            $table->decimal('sale_profit', 15, 2);
            $table->string('store_code');
            $table->timestamps();

            $table->foreign('store_code')->on('stores')->references('code');
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
