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
            if (Schema::hasColumn('transaction_buys', 'supplier')) {
                $table->dropColumn('supplier');
            }

            // Then, add the foreign key constraint for supplier_id
            $table->foreignId('supplier_id')->after('title')->nullable(); // Make it nullable if necessary
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_buys', function (Blueprint $table) {
            $table->string('supplier')->after('title')->nullable();
            $table->dropForeign(['supplier_id']); // Specify the column name as an array
            $table->dropColumn('supplier_id');
        });
    }
};
