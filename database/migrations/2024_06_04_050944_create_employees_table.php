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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code', 6);
            $table->string('full_name');
            $table->string('ktp_number', 16);
            $table->string('ktp_photo');
            $table->foreignId('user_id')->references('id')->on('users')->unique()->cascadeOnDelete();
            $table->timestamp('start_working_at');
            $table->string('store_code', 6);
            $table->string('owner_code', 6);
            $table->timestamps();

            $table->unique(['employee_code', 'store_code']);
            $table->foreign('store_code')->on('stores')->references('code');
            $table->foreign('owner_code')->on('owners')->references('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
