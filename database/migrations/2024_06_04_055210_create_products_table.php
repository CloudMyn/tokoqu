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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('sku');
            $table->integer('sale_price'); // 15 total digits, 2 decimal places
            $table->integer('delivery_fee')->default(0);
            $table->integer('product_cost');
            $table->string('store_code');
            $table->integer('stock')->default(0);
            $table->integer('fraction');
            $table->enum('unit', ['carton', 'pack', 'piece', 'box', 'bag', 'set', 'bottle', 'jar', 'roll', 'case', 'pallet', 'bundle', 'liter', 'milliliter', 'kilogram', 'gram']);
            $table->string('supplier')->nullable();
            $table->timestamps();

            $table->foreign('store_code')->on('stores')->references('code')->cascadeOnDelete();
            $table->unique(['sku', 'store_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
