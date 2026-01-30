<?php

namespace Modules\Logistic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'zone_code',
        'origin_city',
        'destination_city',
        'distance_km',
    ];

    protected $casts = [
        'distance_km' => 'integer',
    ];
}
