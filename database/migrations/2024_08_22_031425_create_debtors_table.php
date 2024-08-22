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
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('paid', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->foreignId('transaction_id')->nullable();
            $table->foreignId('asset_id')->nullable();
            $table->enum('status', ['paid', 'unpaid', 'overdue']);
            $table->date('due_date');
            $table->text('note')->nullable();
            $table->timestamps();


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
