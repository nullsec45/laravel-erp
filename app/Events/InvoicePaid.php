<?php

namespace App\Events;

use Modules\Sales\Models\Invoice;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoicePaid
{
    use Dispatchable, SerializesModels;

    public Invoice $invoice;
    public float $amount;

    /**
     * Create a new event instance.
     */
    public function __construct(Invoice $invoice, float $amount)
    {
        $this->invoice = $invoice;
        $this->amount = $amount;
    }
}
