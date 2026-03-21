<?php

namespace App\Models\Campus;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $connection = 'campus';
    protected $table = 'facilities';
    
    protected $fillable = [
        'name', 
        'description', 
        'location', 
        'capacity', 
        'status',
        'approval_status',
        'thumbnail', 
        'images', 
        'available_hours', 
        'created_by', 
        'latitude', 
        'longitude'
    ];
    
    protected $casts = [
        'images' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];
    
    // Helper methods
    public function isAccepted()
    {
        return $this->approval_status === 'accept';
    }
    
    public function isDeclined()
    {
        return $this->approval_status === 'decline';
    }
    
    public function isPending()
    {
        return $this->approval_status === 'pending';
    }
}