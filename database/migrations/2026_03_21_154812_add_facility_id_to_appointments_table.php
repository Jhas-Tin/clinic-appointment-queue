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
            // Add facility_id column after facility_name
            $table->unsignedBigInteger('facility_id')->nullable()->after('facility_name');
            
            // Add foreign key constraint (optional, but recommended)
            // Note: This references a table in another database, so you may need to omit the foreign key
            // if you're using different database connections
            // $table->foreign('facility_id')->references('id')->on('campus.facilities')->nullOnDelete();
            
            // Add index for faster queries
            $table->index('facility_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Drop the foreign key if added
            // $table->dropForeign(['facility_id']);
            
            // Drop the index
            $table->dropIndex(['facility_id']);
            
            // Drop the column
            $table->dropColumn('facility_id');
        });
    }
};