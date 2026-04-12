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
        Schema::create('damayan_collections', function (Blueprint $table) {
            $table->unsignedBigInteger('collection_id');
            $table->string('powas_id');
            $table->foreign('powas_id')
                ->references('powas_id')
                ->on('powas')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('disbursement_id');
            $table->foreign('disbursement_id')
                ->references('disbursement_id')
                ->on('damayan_disbursements')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->decimal('amount');
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
        Schema::dropIfExists('damayan_collections');
    }
};
