<?php

namespace Modules\Logistic\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_number',
        'delivery_order_id',
        'driver_id',
        'vehicle_id',
        'route_id',
        'departure_time',
        'arrival_time_estimate',
        'arrival_time_actual',
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'arrival_time_estimate' => 'datetime',
        'arrival_time_actual' => 'datetime',
    ];

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function trackingEvents()
    {
        return $this->hasMany(TrackingEvent::class);
    }
}
