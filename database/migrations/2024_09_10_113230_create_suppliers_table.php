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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 6)->unique();
            $table->string('phone')->nullable();
            $table->string('store_code');
            $table->timestamps();

            $table->foreign('store_code')->on('stores')->references('code')->cascadeOnDelete();
        });


        Schema::table('transaction_buys', function (Blueprint $table) {
            $table->dropColumn('supplier');
            $table->foreignId('supplier_id')->after('title')->constrained('suppliers')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');


        Schema::table('transaction_buys', function (Blueprint $table) {
            $table->string('supplier')->after('title')->nullable();
            $table->dropForeign('transaction_buys_supplier_id_foreign');
            $table->dropColumn('supplier_id');
        });
    }
};
