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
        Schema::create('debtors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->integer('amount');
            $table->integer('paid');
            $table->foreignId('transaction_id')->nullable();
            $table->foreignId('asset_id')->nullable();
            $table->enum('status', ['paid', 'unpaid', 'overdue'])->default('unpaid');
            $table->date('due_date');
            $table->text('note')->nullable();
            $table->string('store_code');
            $table->timestamps();


            $table->foreign('store_code')->on('stores')->references('code')->cascadeOnDelete();
            $table->foreign('transaction_id')->on('transaction_sales')->references('id')->onDelete('set null');
            $table->foreign('asset_id')->on('store_assets')->references('id')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debtors');
    }
};
