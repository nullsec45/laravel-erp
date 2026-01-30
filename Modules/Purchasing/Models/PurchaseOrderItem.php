<?php

namespace Modules\Purchasing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Inventory\Models\Product;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'description',
        'quantity',
        'received_quantity',
        'unit_price',
        'tax_rate',
        'discount_rate',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'received_quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'discount_rate' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    // Relationships
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Accessors
    public function getRemainingQuantityAttribute()
    {
        return $this->quantity - $this->received_quantity;
    }

    public function getIsFullyReceivedAttribute()
    {
        return $this->received_quantity >= $this->quantity;
    }

    // Boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (empty($item->received_quantity)) {
                $item->received_quantity = 0;
            }
        });

        static::saving(function ($item) {
            // Calculate total price
            $subtotal = $item->quantity * $item->unit_price;
            $discount = $subtotal * ($item->discount_rate / 100);
            $taxable = $subtotal - $discount;
            $tax = $taxable * ($item->tax_rate / 100);
            $item->total_price = $taxable + $tax;
        });
    }
}
