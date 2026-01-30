<?php

namespace Modules\Logistic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate_number',
        'model',
        'capacity_kg',
        'is_active',
    ];

    protected $casts = [
        'capacity_kg' => 'integer',
        'is_active' => 'boolean',
    ];
}
