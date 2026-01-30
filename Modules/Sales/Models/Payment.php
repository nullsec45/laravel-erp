<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_number',
        'customer_id',
        'invoice_id',
        'user_id',
        'payment_date',
        'amount',
        'payment_method', // cash, bank_transfer, credit_card, check
        'reference_number',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (!$payment->payment_number) {
                $payment->payment_number = 'PAY-' . date('Ymd') . '-' . str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });

        static::saved(function ($payment) {
            // Update invoice paid amount and status
            $invoice = $payment->invoice;
            $totalPaid = $invoice->payments()->sum('amount');
            
            $invoice->paid_amount = $totalPaid;
            
            if ($totalPaid >= $invoice->total) {
                $invoice->status = 'paid';
            } elseif ($totalPaid > 0) {
                $invoice->status = 'partial';
            } else {
                $invoice->status = 'pending';
            }
            
            $invoice->save();
        });
    }
}
