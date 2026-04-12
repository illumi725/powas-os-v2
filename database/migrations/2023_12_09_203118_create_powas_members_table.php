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
        Schema::create('powas_members', function (Blueprint $table) {
            $table->string('member_id')->primary();
            $table->unsignedBigInteger('application_id')->unique();
            $table->foreign('application_id')
                ->references('application_id')
                ->on('powas_applications')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('meter_number')->nullable();
            $table->date('membership_date');
            $table->enum('firstfifty', ['Y', 'N'])->default('N');
            $table->enum('land_owner', ['Y', 'N'])->default('N');
            $table->enum('member_status', ['ACTIVE', 'LOCKED', 'DISCONNECTED', 'REFUNDED'])->default('ACTIVE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('powas_members');
    }
};
