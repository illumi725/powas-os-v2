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
        Schema::create('powas_settings', function (Blueprint $table) {
            $table->id();
            $table->string('powas_id');
            $table->foreign('powas_id')
                ->references('powas_id')
                ->on('powas')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->decimal('water_rate')->default(10);
            $table->decimal('first_50_fee')->default(2500);
            $table->decimal('application_fee')->default(2500);
            $table->decimal('membership_fee')->default(200);
            $table->decimal('minimum_payment')->default(50);
            $table->decimal('members_micro_savings')->default(0);
            $table->integer('due_date_day')->nullable()->default(null);
            $table->integer('reading_day')->nullable()->default(null);
            $table->integer('collection_day')->nullable()->default(null);
            $table->integer('days_before_disconnection')->nullable()->default(null);
            $table->decimal('penalty_per_day')->nullable()->default(null);
            $table->decimal('reconnection_fee')->nullable()->default(null);
            $table->string('land_owners_id')->nullable()->default(null);
            $table->string('bill_paper_size')->nullable()->default('80mm');
            $table->string('receipt_paper_size')->nullable()->default('80mm');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('powas_settings');
    }
};
