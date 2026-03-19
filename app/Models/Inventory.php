<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    // Fields that are mass assignable
    protected $fillable = [
        'name',               // Name of the item (medicine, cotton, etc.)
        'category',           // Category (Medicine, Supply, etc.)
        'quantity',           // Stock quantity
        'unit',               // Unit of measure (pcs, box, bottle)
        'low_stock_threshold' // Minimum stock before alert
    ];
}