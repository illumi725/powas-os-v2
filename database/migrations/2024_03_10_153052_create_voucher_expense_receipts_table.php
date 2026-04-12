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
        Schema::create('voucher_expense_receipts', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('voucher_id');
            $table->foreign('voucher_id')
                ->references('voucher_id')
                ->on('vouchers')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('receipt_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_expense_receipts');
    }
};
