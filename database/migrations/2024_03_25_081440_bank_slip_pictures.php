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
        Schema::create('bank_slip_pictures', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('trxn_id');
            $table->foreign('trxn_id')
                ->references('trxn_id')
                ->on('transactions')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('bank_slip_image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
