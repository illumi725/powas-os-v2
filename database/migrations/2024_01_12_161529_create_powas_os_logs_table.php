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
        Schema::create('powas_os_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('log_id')->primary();
            $table->enum('action_type', ['create', 'update', 'delete']);
            $table->text('log_message');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('powas_id')->nullable()->default(null);
            $table->foreign('powas_id')
                ->references('powas_id')
                ->on('powas')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('log_blade')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('powas_os_logs');
    }
};
