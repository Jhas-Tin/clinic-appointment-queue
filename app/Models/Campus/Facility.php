<?php

namespace App\Models\Campus;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * Relationship: A facility has many reservations.
     */
    public function reservations(): HasMany
    {
        // Pointing to the Reservation model
        return $this->hasMany(\App\Models\Reservation::class, 'facility_id');
    }

    /**
     * Scope: Filter only active and accepted facilities.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'active')
                     ->where('approval_status', 'accept');
    }
    
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

    public function isActive()
    {
        return $this->status === 'active';
    }
}