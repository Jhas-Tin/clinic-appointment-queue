<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Make user_id nullable for receptionist booking
            $table->unsignedBigInteger('user_id')->nullable()->change();

            // Add new columns if not already added
            if (!Schema::hasColumn('appointments', 'emergency_contact')) {
                $table->string('emergency_contact')->nullable()->after('patient_name');
            }
            if (!Schema::hasColumn('appointments', 'parent_guardian')) {
                $table->string('parent_guardian')->nullable()->after('emergency_contact');
            }
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->dropColumn(['emergency_contact', 'parent_guardian']);
        });
    }
};