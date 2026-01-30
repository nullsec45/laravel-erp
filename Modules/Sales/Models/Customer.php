<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_code',
        'name',
        'email',
        'phone',
        'mobile',
        'company',
        'tax_number',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'credit_limit',
        'payment_terms',
        'discount_percentage',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getTotalPurchasesAttribute()
    {
        return $this->invoices()->where('status', 'paid')->sum('total');
    }

    public function getOutstandingBalanceAttribute()
    {
        return $this->invoices()
            ->whereIn('status', ['pending', 'partial'])
            ->sum('total') - $this->payments()->sum('amount');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithOutstanding($query)
    {
        return $query->whereHas('invoices', function ($q) {
            $q->whereIn('status', ['pending', 'partial']);
        });
    }
}
