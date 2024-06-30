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
        Schema::create('transaction_sales', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->string('title');
            $table->decimal('total_amount', 15, 2);
            $table->integer('total_qty');
            $table->decimal('total_profit', 15, 2);
            $table->foreignId('admin_id')->nullable();
            $table->string('admin_name');

            $table->foreignId('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->timestamps();

            $table->string('store_code');

            $table->foreign('admin_id')->on('users')->references('id')->onDelete('set null');
            $table->foreign('store_code')->on('stores')->references('code')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_sales');
    }
};
