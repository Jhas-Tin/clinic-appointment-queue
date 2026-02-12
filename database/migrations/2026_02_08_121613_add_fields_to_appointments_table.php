<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {

            $table->foreignId('user_id')
                  ->after('id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->string('patient_name')->after('user_id');
            $table->string('doctor_name')->after('patient_name');
            $table->date('date')->after('doctor_name');
            $table->time('time')->after('date');

            $table->enum('status', ['Pending', 'Approved', 'Cancelled'])
                  ->default('Pending')
                  ->after('time');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'patient_name',
                'doctor_name',
                'date',
                'time',
                'status'
            ]);
        });
    }
};
