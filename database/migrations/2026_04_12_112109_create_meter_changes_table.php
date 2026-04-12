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
        Schema::create('meter_changes', function (Blueprint $table) {
            $table->id();
            $table->string('member_id');
            $table->foreign('member_id')
                ->references('member_id')
                ->on('powas_members')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('old_meter_number')->nullable();
            $table->string('new_meter_number')->nullable();
            $table->decimal('old_meter_final_reading', 10, 2)->nullable(); // Nullable for broken/unreadable
            $table->decimal('new_meter_start_reading', 10, 2)->default(0);
            $table->date('change_date');
            $table->string('reason')->nullable();
            $table->unsignedBigInteger('recorded_by');
            $table->foreign('recorded_by')
                ->references('user_id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meter_changes');
    }
};
