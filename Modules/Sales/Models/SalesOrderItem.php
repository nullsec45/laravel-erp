<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Models\Product;

class SalesOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_order_id',
        'product_id',
        'description',
        'quantity',
        'unit_price',
        'discount_percentage',
        'discount_amount',
        'tax_percentage',
        'tax_amount',
        'total',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $subtotal = $item->quantity * $item->unit_price;
            $discountAmount = ($subtotal * $item->discount_percentage) / 100;
            $taxableAmount = $subtotal - $discountAmount;
            $taxAmount = ($taxableAmount * $item->tax_percentage) / 100;
            $total = $taxableAmount + $taxAmount;

            $item->discount_amount = $discountAmount;
            $item->tax_amount = $taxAmount;
            $item->total = $total;
        });

        static::saved(function ($item) {
            $item->salesOrder->calculateTotals();
        });

        static::deleted(function ($item) {
            $item->salesOrder->calculateTotals();
        });
    }
}
