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
        Schema::table('doctors', function (Blueprint $table) {

            $table->date('available_date')->nullable()->after('email');

            $table->time('start_time')->nullable()->after('available_date');

            $table->time('end_time')->nullable()->after('start_time');

            $table->string('availability_status')
                ->default('Available')
                ->after('end_time');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {

            $table->dropColumn([
                'available_date',
                'start_time',
                'end_time',
                'availability_status'
            ]);

        });
    }
};