<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];
}