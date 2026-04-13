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
        Schema::table('powas_settings', function (Blueprint $table) {
            $table->string('atp_number')->nullable();
            $table->date('atp_date_issued')->nullable();
            $table->date('atp_valid_until')->nullable();
            $table->string('printer_name')->nullable();
            $table->string('printer_address')->nullable();
            $table->string('printer_tin')->nullable();
            $table->string('printer_accreditation_no')->nullable();
            $table->date('printer_accreditation_date')->nullable();
            $table->string('serial_number_start')->nullable();
            $table->string('serial_number_end')->nullable();
            $table->string('current_serial_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('powas_settings', function (Blueprint $table) {
            $table->dropColumn([
                'atp_number', 'atp_date_issued', 'atp_valid_until', 'printer_name', 'printer_address', 
                'printer_tin', 'printer_accreditation_no', 'printer_accreditation_date', 
                'serial_number_start', 'serial_number_end', 'current_serial_number'
            ]);
        });
    }
};
