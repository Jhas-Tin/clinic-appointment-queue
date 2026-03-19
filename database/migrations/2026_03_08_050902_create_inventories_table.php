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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Name of the item (medicine, cotton, etc.)
            $table->string('category')->nullable(); // Category (Medicine, Supply, etc.)
            $table->integer('quantity')->default(0); // Stock quantity
            $table->string('unit')->nullable(); // Unit of measure (pcs, box, bottle)
            $table->integer('low_stock_threshold')->default(5); // Minimum stock before alert
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};