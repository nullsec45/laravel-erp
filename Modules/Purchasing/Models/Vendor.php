<?php

namespace Modules\Purchasing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'contact_person',
        'email',
        'phone',
        'mobile',
        'website',
        'tax_id',
        'payment_terms',
        'credit_limit',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessors
    public function getTotalPurchasesAttribute()
    {
        return $this->purchaseOrders()
            ->where('status', 'completed')
            ->sum('total_amount');
    }

    public function getOutstandingBalanceAttribute()
    {
        return $this->purchaseOrders()
            ->whereIn('status', ['approved', 'partial'])
            ->sum('outstanding_amount');
    }

    // Boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vendor) {
            if (empty($vendor->code)) {
                $vendor->code = 'VEN-' . str_pad(
                    (Vendor::withTrashed()->max('id') ?? 0) + 1,
                    6,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });
    }
}
