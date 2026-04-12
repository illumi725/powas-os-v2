<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('powas_applications', function (Blueprint $table) {
            $table->unsignedBigInteger('application_id')->primary();
            $table->string('powas_id');
            $table->foreign('powas_id')
                ->references('powas_id')
                ->on('powas')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('lastname', 50);
            $table->string('firstname', 50);
            $table->string('middlename', 50)->nullable();
            $table->date('birthday');
            $table->string('birthplace', 100);
            $table->enum('gender', ['MALE', 'FEMALE'])->default(null);
            $table->string('contact_number', 15);
            $table->enum('civil_status', ['SINGLE', 'MARRIED', 'WIDOW', 'WIDOWER', 'LEGALLY SEPARATED'])->default('SINGLE');
            $table->string('address1', 50);
            $table->string('barangay', 50);
            $table->string('municipality', 50);
            $table->string('province', 50);
            $table->string('region', 50);
            $table->string('present_address', 255);
            $table->integer('family_members');
            $table->enum('application_status', ['PENDING', 'APPROVED', 'REJECTED', 'VERIFIED'])->default('PENDING');
            $table->string('reject_reason', 100)->nullable();
            $table->unsignedBigInteger('by_user_id')->nullable();
            $table->foreign('by_user_id')
                ->references('user_id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->date('application_date');
            $table->enum('add_mode', ['default', 'import', 'manual'])->default('default');
            $table->string('id_path')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('powas_applications');
    }
};
