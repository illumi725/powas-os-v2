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
        Schema::create('billings', function (Blueprint $table) {
            $table->unsignedBigInteger('billing_id')->primary();
            $table->string('powas_id');
            $table->foreign('powas_id')
                ->references('powas_id')
                ->on('powas')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('member_id');
            $table->foreign('member_id')
                ->references('member_id')
                ->on('powas_members')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->unsignedBigInteger('recorded_by');
            $table->foreign('recorded_by')
                ->references('user_id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->unsignedBigInteger('previous_reading_id');
            $table->foreign('previous_reading_id')
                ->references('reading_id')
                ->on('readings')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->unsignedBigInteger('present_reading_id');
            $table->foreign('present_reading_id')
                ->references('reading_id')
                ->on('readings')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->decimal('cubic_meter_used');
            $table->decimal('billing_amount');
            $table->decimal('discount_amount')->default(0);
            $table->decimal('penalty')->default(0);
            $table->date('billing_month');
            $table->date('due_date');
            $table->date('cut_off_start');
            $table->date('cut_off_end');
            $table->integer('bill_number');
            $table->integer('print_count')->default(0);
            $table->enum('bill_status', ['PAID', 'UNPAID', 'PARTIAL', 'PAST DUE'])->default('UNPAID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};
