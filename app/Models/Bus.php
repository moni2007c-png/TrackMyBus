<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bus extends Model
{
    protected $fillable = [
        'bus_number',
        'route',
        'status',
        'latitude',
        'longitude',
        'location_updated_at',
        'destination_latitude',
        'destination_longitude',
        'driver_id',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}