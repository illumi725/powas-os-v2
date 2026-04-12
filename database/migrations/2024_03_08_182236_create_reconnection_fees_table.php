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
        Schema::create('reconnection_fees', function (Blueprint $table) {
            $table->unsignedBigInteger('reconnection_id')->primary();
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
            $table->unsignedBigInteger('billing_id');
            $table->foreign('billing_id')
                ->references('billing_id')
                ->on('billings')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->decimal('amount')->default(0);
            $table->date('date_recorded');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reconnection_fees');
    }
};
