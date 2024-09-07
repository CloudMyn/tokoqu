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
            $table->string('image')->nullable();
            $table->string('title');
            $table->integer('total_amount');
            $table->integer('total_qty');
            $table->integer('total_profit');
            $table->foreignId('admin_id')->nullable();
            $table->string('admin_name');
            $table->boolean('is_debt')->default(false);
            $table->string('store_code');
            $table->timestamps();


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
