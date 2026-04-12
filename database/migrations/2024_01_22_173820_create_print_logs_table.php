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
        Schema::create('issued_receipts', function (Blueprint $table) {
            $table->unsignedBigInteger('print_id')->primary();
            $table->unsignedBigInteger('receipt_number');
            $table->unsignedBigInteger('trxn_id');
            $table->foreign('trxn_id')
                ->references('trxn_id')
                ->on('transactions')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('powas_id');
            $table->foreign('powas_id')
                ->references('powas_id')
                ->on('powas')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->text('description')->nullable()->default(null);
            $table->enum('is_printed', ['YES', 'NO'])->default('NO');
            $table->unsignedInteger('print_count')->default(0);
            $table->date('transaction_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('print_logs');
    }
};
