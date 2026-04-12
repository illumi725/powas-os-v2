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
        Schema::create('damayan_disbursements', function (Blueprint $table) {
            $table->unsignedBigInteger('disbursement_id')->primary();
            $table->string('powas_id');
            $table->foreign('powas_id')
                ->references('powas_id')
                ->on('powas')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('beneficiary_id')->nullable();
            $table->foreign('beneficiary_id')
                ->references('beneficiary_id')
                ->on('damayan_beneficiaries')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('member_id')->nullable();
            $table->foreign('member_id')
                ->references('member_id')
                ->on('powas_members')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('grantee');
            $table->decimal('amount');
            $table->enum('disbursement_status', ['pending', 'collecting', 'disbursed'])->default('pending');
            $table->date('disbursement_date');
            $table->text('death_cert_image');
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
        Schema::dropIfExists('damayan_disbursements');
    }
};
