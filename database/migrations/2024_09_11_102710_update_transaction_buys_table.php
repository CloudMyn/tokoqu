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
        // Updating transaction_buys table
        Schema::table('transaction_buys', function (Blueprint $table) {
            // First, drop the existing supplier column if it exists
            $table->dropColumn('supplier');

            // Then, add the foreign key constraint for supplier_id
            $table->foreignId('supplier_id')->after('title')->constrained('suppliers')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_buys', function (Blueprint $table) {
            $table->string('supplier')->after('title')->nullable();
            $table->dropForeign('transaction_buys_supplier_id_foreign');
            $table->dropColumn('supplier_id');
        });
    }
};
