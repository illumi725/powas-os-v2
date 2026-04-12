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
        Schema::create('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('trxn_id')->primary();
            $table->unsignedBigInteger('account_number');
            $table->foreign('account_number')
                ->references('account_number')
                ->on('chart_of_accounts')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('description');
            $table->string('journal_entry_number')->nullable()->default(0);
            $table->decimal('amount');
            $table->enum('transaction_side', ['DEBIT', 'CREDIT']);
            $table->string('received_from')->nullable();
            $table->string('paid_to')->nullable();
            $table->string('member_id')->nullable()->default(null);
            $table->foreign('member_id')
                ->references('member_id')
                ->on('powas_members')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('powas_id');
            $table->foreign('powas_id')
                ->references('powas_id')
                ->on('powas')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->unsignedBigInteger('recorded_by_id');
            $table->foreign('recorded_by_id')
                ->references('user_id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->date('transaction_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transanctions');
    }
};
