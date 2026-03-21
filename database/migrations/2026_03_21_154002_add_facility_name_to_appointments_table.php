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
        Schema::table('appointments', function (Blueprint $table) {
            // Add facility_name column after doctor_name
            $table->string('facility_name')->nullable()->after('doctor_name');
            
            // Add medicine_quantity column after prescription
            $table->integer('medicine_quantity')->nullable()->after('prescription');
            
            // Add email tracking columns after patient_status
            $table->boolean('email_sent')->default(false)->after('patient_status');
            $table->timestamp('email_sent_at')->nullable()->after('email_sent');
            
            // Optional: Add indexes for better performance
            $table->index('facility_name');
            $table->index('email_sent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Drop the columns if migration is rolled back
            $table->dropColumn('facility_name');
            $table->dropColumn('medicine_quantity');
            $table->dropColumn('email_sent');
            $table->dropColumn('email_sent_at');
            
            // Drop indexes
            $table->dropIndex(['facility_name']);
            $table->dropIndex(['email_sent']);
        });
    }
};