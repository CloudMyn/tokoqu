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
            $table->string('title');
            $table->decimal('total_amount', 15, 2);
            $table->integer('total_qty');
            $table->decimal('total_profit', 15, 2);
            $table->foreignId('employee_id')->nullable();
            $table->string('employee_name');
            $table->timestamps();

            $table->string('store_code');

            $table->foreign('employee_id')->on('employees')->references('id')->onDelete('set null');
            $table->foreign('store_code')->on('stores')->references('code');
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
