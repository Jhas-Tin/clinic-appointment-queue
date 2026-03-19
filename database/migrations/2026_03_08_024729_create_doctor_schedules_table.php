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
        Schema::create('doctor_schedules', function (Blueprint $table) {
            $table->id();
            
            // Link to doctor
            $table->foreignId('doctor_id')
                  ->constrained('doctors')
                  ->onDelete('cascade'); // Delete schedules if doctor deleted
            
            // Day of the week (Monday to Sunday)
            $table->enum('day_of_week', [
                'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
            ]);
            
            // Start and end times
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            
            // Availability status
            $table->enum('availability_status', ['Available', 'Unavailable'])->default('Available');
            
            $table->timestamps();

            // Each doctor can only have one schedule per day
            $table->unique(['doctor_id', 'day_of_week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_schedules');
    }
};