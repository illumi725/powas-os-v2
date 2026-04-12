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
        Schema::create('readings', function (Blueprint $table) {
            $table->unsignedBigInteger('reading_id')->primary();
            $table->string('member_id');
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
            $table->unsignedBigInteger('recorded_by');
            $table->foreign('recorded_by')
                ->references('user_id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->decimal('reading');
            $table->date('reading_date');
            $table->integer('reading_count');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('readings');
    }
};
