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
        Schema::create('powas', function (Blueprint $table) {
            $table->string('powas_id')->primary();
            $table->string('region', 50);
            $table->string('province', 50);
            $table->string('municipality', 50);
            $table->string('barangay', 50);
            $table->string('zone', 50);
            $table->string('phase', 50);
            $table->date('inauguration_date')->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('powas');
    }
};
