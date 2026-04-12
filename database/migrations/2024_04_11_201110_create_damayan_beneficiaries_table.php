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
        Schema::create('damayan_beneficiaries', function (Blueprint $table) {
            $table->unsignedBigInteger('beneficiary_id')->primary();
            $table->string('powas_id');
            $table->foreign('powas_id')
                ->references('powas_id')
                ->on('powas')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('member_id');
            $table->foreign('member_id')
                ->references('member_id')
                ->on('powas_members')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('lastname');
            $table->string('firstname');
            $table->string('middlename')->nullable()->default(null);
            $table->date('birthday')->nullable()->default(null);
            $table->enum('recipient', ['Y', 'N'])->default('N');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('damayan_beneficiaries');
    }
};
